<?php

namespace App\Repositories;

use App\Http\Requests\Subscription\AddSubscriptionRequest;
use App\Models\Subscription;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;

class SubscriptionRepository extends BaseRepository
{
    private $subscription;

    public function __construct()
    {
        $this->subscription = new Subscription();
    }

    public function list($request)
    {
        $query = \DB::table('subscriptions')
            ->select('subscriptions.id', 'subscriptions.name', 'subscriptions.description', 'subscriptions.amount', 'subscriptions.is_active')
            ->whereNull('subscriptions.deleted_at');

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($where) use ($request) {
                $where->where('subscriptions.name', 'like', '%' . $request->search . '%');
                $where->orWhere('subscriptions.description', 'like', '%' . $request->search . '%');
                $where->orWhere('subscriptions.amount', 'like', '%' . $request->search . '%');
            });
        }
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    public function store($request)
    {
        return Subscription::create([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount
        ]);
    }

    public function details($id)
    {
        $dataDetails = $this->subscription->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    public function update($id, $request)
    {
        $data = $this->subscription->find($id);
        $data->update([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount
        ]);
        return $data;
    }

    public function destroy($id)
    {
        return $this->subscription->find($id)->delete();
    }

    public function changeStatus($id, $request)
    {
        $data = $this->subscription->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }
}
