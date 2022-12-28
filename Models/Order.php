<?php
namespace Models;

class Order extends Model {

    protected $table = "orders";

    const PENDING_STATUS   = "pending";
    const COMPLETED_STATUS = "completed";


}