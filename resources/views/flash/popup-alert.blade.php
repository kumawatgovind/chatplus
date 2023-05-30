<script>
@if(session()->has('success'))
    toastr.success("{{session()->get('success')}}");
@elseif(session()->has('error'))
    toastr.error("{{session()->get('error')}}");
@elseif(session()->has('warning'))
    toastr.warning("{{session()->get('warning')}}");
@elseif(session()->has('info'))
    toastr.info("{{session()->get('info')}}");
@endif

</script>