<div class="modal fade ajaxModal" id="quickClientModal" tabindex="-1" role="dialog" aria-labelledby="quickClientLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickClientLabel">Quick Client Entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <form class="ajaxForm" id="quickClientForm" action="{{ route('admin.clients.store') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Client Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="business_name" name="business_name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Primary Contact <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="primary_contact" name="primary_contact" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone1" name="phone1" placeholder="Enter primary phone" required>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Password <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="password" name="password" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Address</label>
            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
          </div>

          <div class="form-group">
            <label>Additional Fields</label>
            <textarea class="form-control summernote" id="additional_fields" name="additional_fields" rows="5"></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Client</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>