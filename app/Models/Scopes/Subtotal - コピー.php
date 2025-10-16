<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Subtotal implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $sql = 'select orders.id as id
                ,(order_details.item_price * order_details.item_pcs + order_details.work_fee) as subtotal
                ,users.id as user_id
                ,users.name as customer_name
                ,staff_users.name as staff_name
                ,orders.staff_id
                ,car_categories.id as car_category_id
                ,car_categories.car_name as car_name
                ,order_details.item_id
                ,items.prod_code
                ,items.item_name
                ,order_details.item_price
                ,items.item_cost
                ,order_details.item_pcs
                ,order_details.work_fee
                ,orders.pitin_date
                ,orders.created_at
                ,orders.updated_at
                from orders
                left join order_details on orders.id = order_details.order_id
                left join items on order_details.item_id = items.id
                left join users on orders.user_id = users.id
                left join users as staff_users on orders.staff_id = staff_users.id
                left join cars on orders.car_id = cars.id
                left join car_categories on cars.car_category_id = car_categories.id
                ';
        $builder->fromsub($sql,'order_subtotal');
    }
}
