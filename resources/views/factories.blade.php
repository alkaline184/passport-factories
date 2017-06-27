<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Passport Challenge Project. Creates tree with factories and the random number of children">
    <meta name="author" content="Alexey Kalinin">
    <link rel="icon" href="../../favicon.ico">

    <title>Passport Challenge Project</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

    <!-- Bootstrap treeview CSS -->
    <link href="{{ asset('/css/bootstrap-treeview.min.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Passport Challenge Project</a>
        </div>
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          @section('alert')
          @show
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
          <h2>Factories</h2>
          <div id="passport-treeview" class=""></div>
        </div>
        <div class="col-sm-3">
        </div>
      </div>

    </div><!-- /.container -->

    <!-- Modal dialog box for factory create/update -->
    <div class="modal fade" id="factoryModal" tabindex="-1" role="dialog" aria-labelledby="factoryModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="factoryModalLabel">Factory</h4>
          </div>
          <div class="modal-body">
          <div class="alert alert-danger" role="alert">
            
          </div>
            <form id="factoryForm">
              <div class="form-group">
                <label for="factory-name" class="control-label">Factory Name:</label>
                <input type="text" class="form-control factory-name" placeholder="Factory Name" id="factory-name">
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group">
                    <label for="lower" class="control-label">Lower Range:</label>
                    <input type="number" class="form-control factory-lower" placeholder="Lower Range (Positive Integer)" id="factory-lower">
                  </div>
                </div>
                <div class="col-xs-6">
                   <div class="form-group">
                      <label for="upper" class="control-label">Upper Range:</label>
                      <input type="number" class="form-control factory-upper" placeholder="Upper Range (Positive Integer)" id="factory-upper">
                    </div>
                </div>
              </div>
              <div class="form-group">
                <label for="count" class="control-label">Children Count:</label>
                <input type="number" class="form-control factory-count" placeholder="Children Count (Maximum 15)" id="factory-count">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary factoryCreateButton">Create</button>
          </div>
        </div>
      </div>
    </div>
   

    <!-- Modal dialog box for factory delete -->
    <div class="modal fade" id="factoryDeleteModal" tabindex="-1" role="dialog" aria-labelledby="factoryDeleteModalLabel">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="factoryDeleteModalLabel">Factory Remove</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to remove this factory?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger factoryRemoveButton">Remove</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.6.0/socket.io.min.js"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap-treeview.min.js') }}"></script>
    <script type="text/javascript">      
      $(function() {
            //Refresh Tree
            refreshTreeView();  

            //Open up a socket connection to redis
            var socket = io.connect("{{config('app.factory_redis_url')}}");
            
            //Update Factories every time there is a message on Redis
            socket.on('factories', function (data) {
              refreshTreeView();
            });

            var testData = [{}];

            //Open up the Modal for Create/Update Factories
            $('#factoryModal').on('shown.bs.modal', function (event) {
              var button = $(event.relatedTarget);
              var modal = $(this)
              var subject = button.data('whatever')

              //Initialize clean modal window
              $(".alert-danger").hide();
              $(".factory-name").val("");
              $(".factory-lower").val("");
              $(".factory-upper").val("");
              $(".factory-count").val("");
              $('.factoryCreateButton').removeAttr('id');

              //If we clicked on Update Button
              if (button.data('factory')){
                $.ajax({url: "/factories/"+ button.data('factory'), success: function(result){
                     testJson = JSON.parse(result);
                     $(".factory-name").val(testJson.name);
                     $(".factory-lower").val(testJson.lower);
                     $(".factory-upper").val(testJson.upper);
                     $(".factory-count").val(testJson.count);
                },
                error: function(){
                    alert('Error while getting Factory.');
                    modal.modal('hide');
                 }});
                $('.factoryCreateButton').attr('id', button.data('factory'));
              }

              modal.find('.modal-title').text(subject + ' Factory ')
              modal.find('.factoryCreateButton').text(subject)
            })

            //Open up the Modal for Delete Factories
            $('#factoryDeleteModal').on('shown.bs.modal', function (event) {
              var button = $(event.relatedTarget);
              $('.factoryRemoveButton').attr('id', button.data('factory'));
            })

            function deleteFactory(factoryId){
              $.ajax({url: "/factories/"+factoryId, type: 'DELETE', 
              success: function(result){
                  refreshTreeView();
              },
              error: function(){
                    alert('Error while deleting Factory.');
                    $('#factoryDeleteModal').modal('hide');
              }});
            }


            $(".factoryRemoveButton").click(function(){
              deleteFactory($(this).attr('id'));
              $('#factoryDeleteModal').modal('hide');
            });

            //function will populate/refresh treeview
            function refreshTreeView(){
              $.ajax({url: "/factories", success: function(result){
                  testJson = JSON.parse(result);
                  $('#passport-treeview').treeview({
                    showTags: true,
                    data: testJson 
                  });
              }});
            }

            $(".factoryCreateButton").click(function(){
              createFactory();
            });

            //function will create/update new factory
            function createFactory(){
              var data = {name: $(".factory-name").val().trim(), lower: $(".factory-lower").val().trim(),
                          upper: $(".factory-upper").val().trim(), count: $(".factory-count").val().trim()};
              var errors = '';
              if (!data.name.trim()){ //If factory name is empty
                errors += 'Factory name can\'t be blank <br />';
              } 
              if (!isPositiveInteger(data.lower)) { //If lower is not an integer
                errors += 'Lower Range should be a Positive Integer <br />';
              } 
              if (!isPositiveInteger(data.upper)) { //If upper is not an integer
                errors += 'Upper Range should be a Positive Integer <br />';
              } 
              if (!isPositiveInteger(data.count) || data.count > 15) { //If count is not an integer or greater than 15
                errors += 'Child Count should be a Positive Integer not greater than 15 <br />';
              } 
              if (parseInt(data.upper) <= parseInt(data.lower)) { //If upper is lower than lower
                errors += 'Upper Range should be greater than Lower Range <br />';
              }

              if (errors){
                  $(".alert-danger").show();
                  $(".alert-danger").html(errors);
              } else {
                  $(".alert-danger").hide();
                  // if we have ID, then update, else create new factory
                  if ($('.factoryCreateButton').attr('id')){
                    $.ajax({url: "/factories/" + $('.factoryCreateButton').attr('id') ,
                       type: 'PUT', 
                       data: data,
                       success: function(result){
                          //Generate Children
                          $.ajax({url: "/factories/" + $('.factoryCreateButton').attr('id') + '/children' ,
                              type: 'PUT', 
                              success: function(result){
                                refreshTreeView();
                              },
                              error: function(){
                                alert('Error while adding children to the Factory.');
                                $('#factoryModal').modal('hide');
                              }
                          });
                       }, 
                       error: function(){
                            alert('Error while updating Factory.');
                            $('#factoryModal').modal('hide');
                       }
                  });
                  } else {
                    $.ajax({url: "/factories",
                        type: 'POST', 
                        data: data,
                        success: function(result){
                          factoryJson = JSON.parse(result);
                          //Generate Children
                          $.ajax({url: "/factories/" + factoryJson._id + '/children' ,
                             type: 'PUT', 
                             success: function(result){
                                refreshTreeView();
                              },
                              error: function(){
                                    alert('Error while adding children to the Factory.');
                                    $('#factoryModal').modal('hide');
                              }
                          });
                        },
                        error: function(){
                            alert('Error while creating Factory.');
                            $('#factoryModal').modal('hide');
                       }
                    });
                  }
                  $('#factoryModal').modal('hide');
              }

            }

            //Main function that builds a TreeView component
            $('#passport-treeview').treeview({
              showTags: true,
              data: testData 
            });

            function isPositiveInteger(str) {
                var n = Math.floor(Number(str));
                return String(n) === str && n >= 0;
            }
      });
    </script>
  </body>
</html>
