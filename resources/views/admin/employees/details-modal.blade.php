<div class="modal fade" id="detailsModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $row->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel{{ $row->id }}">Employee Details - {{ $row->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <h5>Basic Information</h5><hr>
          </div>
          <div class="col-md-4"><p><strong>Name:</strong> {{ $row->name }}</p></div>
          <div class="col-md-4"><p><strong>Email:</strong> {{ $row->email }}</p></div>
          <div class="col-md-4"><p><strong>Contact No:</strong> {{ $row->contact_no }}</p></div>
          <div class="col-md-3"><p><strong>Joining Date:</strong> {{ $row->joining_date ?? '-' }}</p></div>
          <div class="col-md-3"><p><strong>Em. Contact Person:</strong> {{ $row->em_contact_person ?? '-' }}</p></div>
          <div class="col-md-3"><p><strong>Em. Contact No:</strong> {{ $row->em_contact_no ?? '-' }}</p></div>
          <div class="col-md-3"><p><strong>NID:</strong> {{ $row->nid ?? '-' }}</p></div>
          <div class="col-md-12"><p><strong>Address:</strong> {{ $row->address ?? '-' }}</p></div>

          <div class="col-12 mt-3">
            <h5>Payment Information</h5><hr>
          </div>
          <div class="col-md-4"><p><strong>Salary:</strong> {{ $row->salary ?? '-' }}</p></div>
          <div class="col-md-8"><p><strong>Bank Details:</strong> {{ $row->bank_details ?? '-' }}</p></div>
        </div>
      </div>
    </div>
  </div>
</div>