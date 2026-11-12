<?php

namespace App\Domains\Production\Services;

use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\Orders\Models\Order;
use App\Domains\Production\Models\ProductionJob;
use App\Domains\Production\Models\ProductionJobStage;
use App\Domains\Production\Models\ProductionStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductionService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createJob(array $data, ?User $actor = null): ProductionJob
    {
        return DB::transaction(function () use ($data, $actor) {
            $job = ProductionJob::query()->create([
                'job_number' => $this->generateJobNumber(),
                'custom_order_id' => $data['custom_order_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $actor?->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => 'pending',
                'expected_completion_date' => $data['expected_completion_date'] ?? null,
                'quantity' => (int) ($data['quantity'] ?? 1),
                'notes' => $data['notes'] ?? null,
            ]);

            $stages = ProductionStage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($stages as $stage) {
                ProductionJobStage::query()->create([
                    'production_job_id' => $job->id,
                    'production_stage_id' => $stage->id,
                    'status' => 'pending',
                ]);
            }

            if (! empty($data['custom_order_id'])) {
                CustomOrder::query()
                    ->whereKey($data['custom_order_id'])
                    ->update(['status' => 'in_production']);
            }

            return $job->fresh(['jobStages.productionStage', 'customOrder', 'order']);
        });
    }

    public function updateStage(ProductionJobStage $jobStage, string $status, ?string $notes = null): ProductionJobStage
    {
        $allowed = ['pending', 'in_progress', 'completed'];
        if (! in_array($status, $allowed, true)) {
            throw new RuntimeException('Invalid stage status.');
        }

        return DB::transaction(function () use ($jobStage, $status, $notes) {
            $updates = [
                'status' => $status,
                'notes' => $notes ?? $jobStage->notes,
            ];

            if ($status === 'in_progress' && ! $jobStage->started_at) {
                $updates['started_at'] = now();
            }

            if ($status === 'completed') {
                $updates['completed_at'] = now();
            }

            $jobStage->update($updates);

            $job = $jobStage->productionJob;
            if ($job) {
                $allCompleted = $job->jobStages()->where('status', '!=', 'completed')->doesntExist();
                if ($allCompleted) {
                    $job->update(['status' => 'completed']);
                } elseif ($status === 'in_progress' && $job->status === 'pending') {
                    $job->update(['status' => 'in_progress']);
                }
            }

            return $jobStage->fresh('productionStage');
        });
    }

    public function createForOrder(Order $order, ?User $actor = null): ProductionJob
    {
        return $this->createJob([
            'order_id' => $order->id,
            'title' => 'Order '.$order->order_number,
            'description' => 'Production for storefront order',
            'quantity' => (int) $order->items->sum('quantity'),
        ], $actor);
    }

    private function generateJobNumber(): string
    {
        do {
            $number = 'JOB-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (ProductionJob::query()->where('job_number', $number)->exists());

        return $number;
    }
}
