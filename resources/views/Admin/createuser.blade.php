@include('Admin.header')
        <!-- partial -->


        
        <div class=" grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Create User</h4>
         
                    <form class="forms-sample" action="{{ route('create-user') }}" method="post">
                      @csrf
                    <div class="form-group">
                        <label for="exampleInputUsername1">Username</label>
                        <input type="text" class="form-control" required name="username" id="exampleInputUsername1" placeholder="Username">
                      </div>  <div class="form-group">
                        <label for="exampleInputUsername1">Username Owner Name</label>
                        <input type="text" class="form-control" required name="username_owner_name" id="exampleInputUsername1" placeholder="Username">
                      </div>  <div class="form-group">
                        <label for="exampleInputUsername1">Roll</label>
                        <input type="text" class="form-control" required name="roll" id="exampleInputUsername1" placeholder="Username">
                      </div>  <div class="form-group">
                        <label for="exampleInputUsername1">Credite limit</label>
                        <input type="text" class="form-control" required name="credite_limit" id="exampleInputUsername1" placeholder="Username">
                      </div>  <div class="form-group">
                        <label for="exampleInputUsername1">Cash Balance</label>
                        <input type="text" class="form-control" required name="cash_balance" id="exampleInputUsername1" placeholder="Username">
                      </div><div class="form-group">
                        <label for="exampleInputUsername1">Outtanding Transaction</label>
                        <input type="text" class="form-control" required name="outtanding_transaction" id="exampleInputUsername1" placeholder="Username">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" required name="email" id="exampleInputEmail1" placeholder="Email">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" required name="password" id="exampleInputPassword1" placeholder="Password">
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Status</label>
                       <Select class="form-control text-white" required name="status">
                        <option value="active" selected>Active</option>
                        <option value="disable">Disable</option>
                       </Select>
                      </div>
                    
                      <button type="submit" class="btn btn-primary mr-2">Submit</button>
                      
                    </form>
                  </div>
                </div>
              </div>

     


 @include('Admin.footer')
   