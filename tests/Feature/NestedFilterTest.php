<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filters\NestedFilter;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NestedFilterTest extends TestCase
{
    use RefreshDatabase;

    public function testNestedEloquentFilter(): void
    {
        /*
         * Apply json_decode($json, true)
         * {
         *  "patient.name": "David",
         *  "appointment.status": "submitted",
         *  "location.name": "Location2"
         * }
        */
        $json = '{"patient.name": "David","appointment.status": "submitted","location.name": "Location2"}';

        $jsonFilters = json_decode($json, true);

        $sql = 'select * from "appointments" where exists (select * from "users" where "appointments"."user_id" = "users"."id" and "name" = \'David\' and "role" = \'patient\') and "appointments"."status" = \'submitted\' and exists (select * from "locations" where "appointments"."location_id" = "locations"."id" and "name" = \'Location2\')';

        $rawSql = $this->getNestedFilterQuery($jsonFilters)->toRawSql();

        $this->assertEquals($sql, $rawSql);
    }

    public function testNestedEloquentWithOperatorsFilter(): void
    {
        /*
         * Apply json_decode($json, true)
         * {
         *  "patient.name": {
         *      "operator": "like",
         *      "value": "David"
         *  },
         *  "appointment.status": {
         *      "operator": "like",
         *      "value": "%sub%"
         *  },
         *  "location.name": "Location2"
         * }
        */
        $json = '{"patient.name":{"operator":"like","value":"David"},"appointment.status":{"operator":"like","value":"%sub%"},"location.name":"Location2"}';
        $jsonFilters = json_decode($json, true);

        $sql = 'select * from "appointments" where exists (select * from "users" where "appointments"."user_id" = "users"."id" and "name" like \'David\' and "role" = \'patient\') and "appointments"."status" like \'%sub%\' and exists (select * from "locations" where "appointments"."location_id" = "locations"."id" and "name" = \'Location2\')';

        $rawSql = $this->getNestedFilterQuery($jsonFilters)->toRawSql();

        $this->assertEquals($sql, $rawSql);
    }

    public function testNestedEloquentWhereHasFilter(): void
    {
        /*
         * Apply json_decode($json, true)
         * {
         *  "and": [
         *      {"patient.name": {
         *          "operator": "like",
         *          "value": "David"
         *      }},
         *      {"appointment.status": {
         *          "operator": "like",
         *          "value": "%sub%"
         *      }},
         *  ]
         * }
        */
        $json = '{"and":[{"patient.name":{"operator":"like","value":"David"}},{"appointment.status":{"operator":"like","value":"%sub%"}}]}';

        $jsonFilters = json_decode($json, true);

        $sql = 'select * from "appointments" where exists (select * from "users" where "appointments"."user_id" = "users"."id" and "name" like \'David\' and "role" = \'patient\') and "appointments"."status" like \'%sub%\'';

        $rawSql = $this->getNestedFilterQuery($jsonFilters)->toRawSql();

        $this->assertEquals($sql, $rawSql);
    }

    public function testNestedEloquentOrWhereHasFilter(): void
    {
        /*
         * Apply json_decode($json, true)
         * {
         *  "or": [
         *      {"patient.name": {
         *          "operator": "like",
         *          "value": "David"
         *      }},
         *      {"location.state.name": {
         *          "operator": "like",
         *          "value": "State3"
         *      }},
         *  ]
         * }
        */
        $json = '{"or":[{"patient.name":{"operator":"like","value":"David"}},{"location.state.name":{"operator":"like","value":"State3"}}]}';
        $jsonFilters = json_decode($json, true);

        $sql = 'select * from "appointments" where exists (select * from "users" where "appointments"."user_id" = "users"."id" and "name" like \'David\' and "role" = \'patient\') or exists (select * from "locations" where "appointments"."location_id" = "locations"."id" and exists (select * from "states" where "locations"."state_id" = "states"."id" and "name" like \'State3\'))';

        $rawSql = $this->getNestedFilterQuery($jsonFilters)->toRawSql();

        $this->assertEquals($sql, $rawSql);
    }

    public function testNestedEloquentAndOrFilter(): void
    {
        /*
         * Apply json_decode($json, true)
         * {
         *  "or": [
         *      {"patient.name": {
         *          "operator": "like",
         *          "value": "David"
         *      }},
         *      {"location.state.name": {
         *          "operator": "like",
         *          "value": "State3"
         *      }},
         *  ],
         *  "and": [
         *      {"location.name": "Location3"}
         *  ]
         * }
        */
        $json = '{"or":[{"patient.name":{"operator":"like","value":"David"}},{"location.state.name":{"operator":"like","value":"State3"}}],"and":[{"location.name":"Location3"}]}';
        $jsonFilters = json_decode($json, true);

        $sql = 'select * from "appointments" where exists (select * from "users" where "appointments"."user_id" = "users"."id" and "name" like \'David\' and "role" = \'patient\') or exists (select * from "locations" where "appointments"."location_id" = "locations"."id" and exists (select * from "states" where "locations"."state_id" = "states"."id" and "name" like \'State3\')) and exists (select * from "locations" where "appointments"."location_id" = "locations"."id" and "name" = \'Location3\')';

        $rawSql = $this->getNestedFilterQuery($jsonFilters)->toRawSql();

        $this->assertEquals($sql, $rawSql);
    }

    private function getNestedFilterQuery(array $filters): Builder
    {
        $this->seed();
        return new NestedFilter(Appointment::query())
            ->apply($filters);
    }
}
