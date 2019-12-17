<!DOCTYPE html>
<head>
  <title>Language Translations Exporter</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

</head>
<style>
    body {
        background: #5A3638;
        color: #ffffff;
        font-family: sans-serif;
    }


    .lds-ripple {
        display: inline-block;
        position: absolute;
        width: 80px;
        height: 80px;
        left: 48%;
        top: 50%;
        display: none;
    }
    .lds-ripple div {
        position: absolute;
        border: 4px solid #fff;
        opacity: 1;
        border-radius: 50%;
        animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }
    .lds-ripple div:nth-child(2) {
        animation-delay: -0.5s;
    }
    @keyframes lds-ripple {
        0% {
            top: 36px;
            left: 36px;
            width: 0;
            height: 0;
            opacity: 1;
        }
        100% {
            top: 0px;
            left: 0px;
            width: 72px;
            height: 72px;
            opacity: 0;
        }
    }
</style>
<body>
<div class="container">
    <h3 class="text-center">Language Translations Exporter</h3>
    <br>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <form class="">
                <div class="form-group">
                    <label for="api_token">API Token:</label>
                    <input name="api_token" required type="text" class="form-control" id="api_token">
                </div>
                <div class="form-group">
                    <label for="project_id">Project ID:</label>
                    <input name="project_id" required type="text" class="form-control" id="project_id">
                </div>
                <div class="form-group">
                    <label for="format">Format:</label>
                    <select name="format" required class="form-control" id="format">
                        <option value="po">.po</option>
                        <option value="pot">.pot</option>
                        <option value="mo">.mo</option>
                        <option value="xls">.xls</option>
                        <option value="xlsx">.xlsx</option>
                        <option value="csv">.csv</option>
                        <option value="ini">.ini</option>
                        <option value="resw">.resw</option>
                        <option value="resx">.resx</option>
                        <option value="xml">.xml</option>
                        <option value="strings">.strings</option>
                        <option value="xliff">.xliff</option>
                        <option value="properties">.properties</option>
                        <option value="php">.php</option>
                        <option value="json">.json</option>
                        <option value="yml">.yml</option>
                        <option value="xmb">.xmb</option>
                        <option value="xtb">.xtb</option>
                    </select>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">
                        Generate
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<div class="lds-ripple"><div></div><div></div><div></div><div></div>
</div>

<script>
    $(document).ready(function(){
        init_toaster();

        $('form').submit(function(e){
            e.preventDefault();
            $("button[type=submit]").prop("disabled", true);
            $(".lds-ripple").show();
            $(".container").css("opacity", "0.2");
            $.ajax({
                url: 'generate-zip-languages.php',
                method: 'POST',
                data: $(this).serialize(),
                xhrFields:{
                    responseType: 'blob'
                },
                success: function(data) {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = 'languages.zip';
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    toastr.success('Zip file has been successfully generated.');
                    $("button[type=submit]").prop("disabled", false);
                    $(".container").css("opacity", "1");
                    $(".lds-ripple").hide();
                },
                error: function(err) {
                    toastr.error('Error on generating zip file');
                    console.log(err);
                }
            });
        });
    });

    function init_toaster() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }
</script>

</body>
