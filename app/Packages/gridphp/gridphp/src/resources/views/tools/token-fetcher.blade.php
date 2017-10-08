<script>

    //TODO include this script as file ont the core, require it conditionally if a Laravel app

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    function tokenRefresh() {

        $.ajax({
            method: "GET",
            url: window.location.href,
            data: {
                _tokenRefresh: true
            }
        }).fail(function(response, textStatus){
            alert("Out of connectivity. Please check your internet connection.");
        }).done(function(response){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': response.data.token
                }
            });
        });
    }

    $(document).ready(function(){
        setInterval(tokenRefresh, 5*60*1000);
    });

</script>