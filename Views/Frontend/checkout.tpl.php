
    <div class="row mb-4">
       <?php alert();?>
        <div class="col-md-4 order-md-2">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
                <span class="badge badge-secondary badge-pill"><?=count($cart)?></span>
            </h4>
            <ul class="list-group mb-3">
               <?php $total = 0;foreach ($cart as $product):?>
                   <li class="list-group-item d-flex justify-content-between lh-condensed">
                       <div>
                           <h6 class="my-0"><?=$product->name?></h6>
                           <small class="text-muted"><?=$product->description?></small>
                       </div>
                       <span class="text-muted">$<?=$product->price; $total+=$product->price;?></span>
                   </li>
               <?php endforeach;?>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (USD)</span>
                    <strong>$<?=$total?></strong>
                </li>
            </ul>
        </div>
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Your Cart</h4>
            <form class="needs-validation"  action="/checkout" method="post">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="firstName">Products</label>
                            <?php
                            foreach ($cart as $product): ?>
                            <div class="row" id="product-<?=$product->id?>">
                                <div class="col-sm-8">
                                    <label for="productName<?=$product->id?>">ProductName</label>
                                    <input type="text" value="<?=$product->name?>" class="form-control" id="productName<?=$product->id?>" readonly disabled >
                                </div>
                                <div class="col-sm-2">
                                    <label for="productQuantity<?=$product->id?>">ProductQuantity</label>
                                    <input type="number" min="1" step="1"  value="1" class="form-control" id="productQuantity<?=$product->id?>" name="products[<?=$product->id?>]" >
                                </div>
                                <div class="col-sm-2">
                                    <label for="removeProduct<?=$product->id?>" style="visibility: hidden">remove</label>
                                    <button class="btn btn-sm btn-danger" onclick="removeFromCart('product-<?=$product->id?>')" style="padding: 10px;">REMOVE</button>
                                </div>
                            </div>
                            <?php endforeach;?>
                    </div>
                    <h4 class="mb-3">Shipping address</h4>

                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name</label>
                        <input type="text" class="form-control" id="firstName" name="firstname" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="lastname" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>

                <div class="mb-3">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="country">Country</label>
                        <select class="custom-select d-block w-100" id="country" name="country" required>
                            <option value="">Choose...</option>
                            <option>Egypt</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="state">State</label>
                        <select class="custom-select d-block w-100" id="state" name="state" required>
                            <option value="">Choose...</option>
                            <option>Tanta</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="zip">Zip</label>
                        <input type="text" class="form-control" id="zip" name="zip" required>
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
            </form>
        </div>
    </div>


    <script>
        function removeFromCart(product) {
            // send to ajax to remove it from session or from database if exists
            document.getElementById(product).remove();
        }
    </script>