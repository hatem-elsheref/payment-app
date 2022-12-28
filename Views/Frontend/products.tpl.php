<div class="row">

    <?php foreach ($products as $product):?>
    <div class="col-sm-3">
        <div class="card" style="width: 18rem;">
            <img src="<?php echo load_frontend_assets($product->image)?>" class="card-img-top" alt="" style="height: 250px;">
            <div class="card-body">
                <h5 class="card-title"><?=$product->name?>  <span class="text-danger"><?=$product->price?></span></h5>
                <p class="card-text"><?=$product->description?></p>
                <a href="#" class="btn btn-primary">Add To Cart</a>
                <a href="/buy-now/<?=$product->id?>" class="btn btn-danger">Buy Now</a>
            </div>
        </div>
    </div>
    <?php endforeach;?>

</div>