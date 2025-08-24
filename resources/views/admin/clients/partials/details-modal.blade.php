<div class="modal fade" id="detailsModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $row->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="detailsModalLabel-{{ $row->id }}">{{ $row->business_name }}</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="p-3 border rounded bg-light">
          <div class="row">
            <div class="col-6">
              <h3>Address</h3>
              <p>{!! $row->address !!}</p>
            </div>
            <div class="col-6">
              <h3>Additional Information</h3>
              <p>{!! $row->additional_fields !!}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>