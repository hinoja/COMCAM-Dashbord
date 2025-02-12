<!-- small modal -->
<div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">
                    @if ($subscriptions_count === 1)
                        @lang('Validate the subscription of')
                    @else
                        @lang('Validate the subscription renewal of')
                    @endif
                    <span id="modal-subscriber_name"></span>
                    {{-- <strong>{{ $subscriber->id }}</strong> --}}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <p class="text-danger font-weight-bold">@lang('Are you sure you want to validate this subscription ?')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn-primary" href="#" id="modal-confirm_validation">@lang('Confirm')</button>
            </div>
        </div>
    </div>
</div>

<script>
    function loadDeleteModal(id, name) {

        $('#modal-subscriber_name').html(name);
        $('#modal-confirm_validation').attr('onclick', `confirmDelete(${id})`);

        $('#smallModal').modal('show');
    }

    function confirmDelete(id) {

        var url = "{{ route('admin.subscribers.validate', ':id') }}";
        url = url.replace(':id', id);
        this.disabled = true;
        location.href = url;

    }
</script>
