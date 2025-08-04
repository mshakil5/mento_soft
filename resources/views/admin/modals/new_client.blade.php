<div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newClientModalLabel">Add New Client</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="newClientForm">
          <div class="form-group">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Business Name</label>
            <input type="text" name="business_name" class="form-control">
          </div>
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone1" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-success">Save Client</button>
        </form>
      </div>
    </div>
  </div>
</div>