<style>
.widget-user-header{
    background-position: center center;
    background-size: cover;
    height: 250px !important;
}
.widget-user .card-footer{
    padding: 0;
}

</style>


<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header text-white" style="background-image:url('./img/user-cover.jpg')">
                    <h3 class="widget-user-username">{{ form.name }}</h3>
                    <h5 class="widget-user-desc">ABBY</h5>
                </div>
                <div class="widget-user-image">
                    <img class="img-circle"  alt="User Avatar">
                </div>
                <div class="card-footer">
                    <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">SALES</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                        <h5 class="description-header">13,000</h5>
                        <span class="description-text">FOLLOWERS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4">
                        <div class="description-block">
                        <h5 class="description-header">35</h5>
                        <span class="description-text">PRODUCTS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                </div>
            </div>

            <!-- tab -->

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                        <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- activity tab -->
                        <div class="tab-pane" id="activity">
                            <h3 class="text-center">Display User Activity</h3>                        
                        </div>

                        <!-- settings tab-->
                        <div class="tab-pane active show" id="settings">
                            <form class="form-horizontal">

                             <!-- name    -->
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Name</label>

                                <div class="col-sm-10">
                                  <input type="name"  v-model="form.name" class="form-control" id="inputName" placeholder="Name"
                                  :class="{ 'is-invalid': form.errors.has('name') }">
                                  <has-error :form='form' field='name'></has-error>

                                </div>
                            </div>

                            <!-- email -->
                            <div class="form-group">
                                <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                                <div class="col-sm-10">
                                    <input type="email" class="form-control"  
                                        v-model="form.email" 
                                        id="inputEmail" 
                                        placeholder="Email"
                                        :class="{ 'is-invalid': form.errors.has('email') }"
                                    >
                                    <has-error :form='form' field='email'></has-error>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div class="form-group">
                                <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                                <div class="col-sm-10">
                                     <textarea class="form-control" id="inputExperience" placeholder="Experience"
                                      v-model="form.bio"
                                     :class="{ 'is-invalid': form.errors.has('inputExperience') }"></textarea>
                                     <has-error :form='form' field='inputExperience'></has-error>

                                </div>
                            </div>

                            <!-- profile photo -->
                            <div class="form-group">
                                <label for="photo" class="col-sm-2 control-label">Profile Photo</label>
                                <div class="col-sm-12">
                                    <input type="file" @change="updateProfile" name="photo" class="form-input">
                                </div>
                            </div>

                            <!-- passport -->
                            <div class="form-group">
                                <label for="password" class="col-sm-12 control-label">Passport (leave empty if not changing)</label>

                                <div class="col-sm-12">
                                    <input type="password" v-model="form.password"
                                        class="form-control"
                                        id="password"
                                        placeholder="Passport"
                                        :class="{ 'is-invalid': form.errors.has('password') }"
                                    >
                                    <has-error :form='form' field='password'></has-error>
                                </div>
                            </div>
                           
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" @click.prevent="updateInfo" class="btn btn-success">Update</button>
                                </div>
                            </div>

                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>
                <!-- /.nav-tabs-custom -->
          </div>
          <!-- end tabs -->
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

            }
        },

        methods:{
            updateInfo: function () {
                this.$Progress.start();
                this.form.put('api/profile/')
                .then(() => {

                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },

            updateProfile: function(e) {
                // console.log('dada');
                let file = e.target.files[0];

                let reader = new FileReader();

                if ( file['size'] < 21111755 ) //2mb
                {
                    reader.onloadend = (file) => {
                        // console.log('tata', reader.result);
                        // console.log(reader);
                        this.form.photo = reader.result;
                        
                    }
                    reader.readAsDataURL(file);
                    
                } 
                else 
                {
                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'File size must be less than 2MB',

                    })
                }

            },
        },

        mounted() {
            console.log('Component mounted.')
        },

        created(){
            console.log('Component created.');
            
            axios.get('api/profile')
            .then(
                  ({ data }) => (this.form.fill(data))
            );  
        },
    }
</script>