<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              
              <div class="card-header">
                <h3 class="card-title">Users List</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">
                          Add New 
                      <i class="fas fa-user-plus fa-fw"></i> 
                    </button>
                </div>
                
              </div>
              
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Created At </th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="user in users" :key="user.id">
                    <td> {{ user.id }} </td>
                    <td> {{ user.name }} </td>
                    <td> {{ user.email }} </td>
                    <td> {{ user.type | upText }} </td>
                    <td> {{ user.created_at | customDateFormat }} </td>
                    <td>
                        <!-- update user -->
                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <!-- delete -->
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>







        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editMode" id="addNewLabel">Add New</h5>
                        <h5 class="modal-title" v-show="editMode" id="addNewLabel">Update User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent=" editMode ? updateUser() :createUser()">
                        <div class="modal-body">
                        
                            <!-- username -->
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder="username" autofocus
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            
                            <!-- email -->
                            <div class="form-group">
                                <input v-model="form.email" type="text" name="email" placeholder="email"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <!-- bio -->
                            <div class="form-group">
                                <textarea v-model="form.bio" name="bio" id="bio"
                                placeholder="Short bio for user (Optional)"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                                </textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>

                            <!-- userrole -->
                            <div class="form-group">
                                <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password" id="password" placeholder="password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>

                         </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" v-show="editMode" class="btn btn-success">Update</button>
                            <button type="submit" v-show="!editMode" class="btn btn-primary">Create</button>
                        </div>
                    </form>        

                </div>
            </div>
        </div>        






    </div>
</template>

<script>
    export default {

        data(){
            return{

                form: new Form({
                     id:'',
                     name:'',
                     email:'',
                     password:'',
                     type:'',
                     bio:'',
                     photo:''
                }),

                editMode: false,
                users:{},
            }
        },

        methods:{
            updateUser: function() {
                this.$Progress.start();

                this.form.put('api/user/'+ this.form.id)
                .then(() => {

                    // success
                    $('#addNew').modal('hide');
                    swal.fire(
                        'Updated!',
                        'User information has been updated.',
                        'success'
                    )

                    Fire.$emit('afterCreate');

                    this.$Progress.finish();

                })
                .catch(()=>{
                    this.$Progress.fail();

                });
            },

            newModal: function() {
                this.editMode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            editModal: function(user) {
                this.editMode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },

            createUser: function(){
                //  [App.vue specific] When App.vue is first loaded start the progress bar
                this.$Progress.start();
                this.editMode = false,

                   this.form.post('api/user')
                   .then(() =>{

                       //    console.log(response);
                          
                           // swal.fire(
                           //     'Good job!',
                           //     'User created succesfully!',
                           //     'success'
                           // );
                            
                           //generate events
                           Fire.$emit('afterCreate'); 
       
                           $('#addNew').modal('hide');
                           toast.fire({
                               type: 'success',
                               title: 'User created successfully'
                           });
       
                           this.$Progress.finish();
                   })
                   .catch(()=>{

                   });
            },

            loadUsers: function() {

                if(this.$gate.isAdmin()){
                    axios.get('api/user').then(
                        ({ data }) => (this.users = data.data)
                        // ({ data }) => (this.users = data.data)
                    );  
                }
            },

            //deleteUser
            deleteUser: function(userId) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {  
                        if (result.value) 
                        {                            
                            // send req to server
                            this.form.delete('api/user/'+userId).then(() =>{                            
                                swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                                Fire.$emit('afterCreate'); 
                            }).catch( ()=> {
                                swal("Failed!", "There was something wronge.", "warning");
                            });
                        }
                      
                })
            },
        },

        // lifecycle hooks
        // mounted() {
        //     console.log(this.loadUsers());
        // },

        created() {
            this.loadUsers();
            
            //listen events
            Fire.$on('afterCreate', () =>{
                this.loadUsers();
            });
            // setInterval( ()=>  this.loadUsers() ,3000);
        }
    }
</script>
