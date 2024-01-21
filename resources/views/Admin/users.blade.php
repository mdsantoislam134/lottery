@include('Admin.header')
        <!-- partial -->


        
     
        
              <div class=" grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Users</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Id</th>
                            <th>User Name</th>
                            <th>Credite limit</th>
                            <th>Available credite</th>
                            <th>Credite used</th>
                            <th>Edit User</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach($users as $user)
                          <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->username}}</td>
                            <td>{{$user->credite_limit}}</td>
                            <td>{{$user->available_credit}}</td>
                            <td>{{$user->credit_used}}</td>
                            <td>
                            <a href="{{url('edit-user',$user->id)}}" class="btn badge-success">Edit</a>
                              <a href="{{url('Recharge',$user->id)}}" class="btn badge-primary">Recharge</a>
                            </td>
                          </tr>
@endforeach

                         
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

     


 @include('Admin.footer')