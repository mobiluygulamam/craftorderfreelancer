<div class="modal-body">
     <div class="row">
         <div class="col-md-12 col-12 text-center">
             <span class="invite-warning"></span>
         </div>
      
         <!-- Proje Dropdown'ı -->
         <div class="col-md-12 form-group">
             <label class="col-form-label">{{ __('Project') }}</label><x-required></x-required>
             <select class="form-control form-control-light" name="project_id" id="task-project" required>
                 @foreach($projects as $project)
                     <option value="{{ $project->id }}">{{ $project->name }}</option>
                 @endforeach
             </select>
         </div>
 
 
  
     </div>
 </div>
 
 <div class="col-md-12 modal-footer">
     <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
     <button type="button" class="btn btn-primary go_taskboard">{{ __('Go') }}</button>
 </div>
 

     <script type="text/javascript">
    $(document).ready(function() {
        // Invite Butonuna Tıklanma
        $('.go_taskboard').on('click', function() {
            var projectId = $('#task-project').val();  // Seçilen projenin ID'sini alıyoruz
          
      
          var redirectUrl = '/kadir/projects/' + projectId + '/task-board';  
        
               window.location.href = redirectUrl;  

     
         
         
        
        });
    });


 </script>