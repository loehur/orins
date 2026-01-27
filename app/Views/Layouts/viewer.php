<style>
    .disabled_all {
        pointer-events: none;
        opacity: 0.8;
    }

    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #04871e;
        border-right: 16px solid #abf1b9;
        border-bottom: 16px solid #ffffff;
        border-left: 16px solid #3193df;
        width: 100px;
        height: 100px;
        -webkit-animation: spin 1s linear infinite;
        /* Safari */
        animation: spin 1s linear infinite;
    }

    .loaderDiv {
        position: fixed;
        z-index: 1;
        left: 40%;
        top: 30%;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="loaderDiv">
    <div class="loader"></div>
</div>

<script>
    $(document).ready(function() {
        content();
    });

    function content(new_parse = "", new_parse_2 = "") {
        $('div.loaderDiv').removeClass('d-none');

        if (new_parse != "") {
            parse = new_parse
        } else {
            parse = '<?= $data['parse'] ?>';
        }

        if (new_parse_2 != "") {
            parse_2 = new_parse_2
        } else {
            parse_2 = '<?= isset($data['parse_2']) ? $data['parse_2'] : '' ?>';
        }
        page = "<?= isset($data['page']) && $data['page'] != "" ? $data['page'] : 'content' ?>";
        if (parse_2 != "") {
            $("div#content").load('<?= PV::BASE_URL ?><?= $data["controller"] ?>/' + page + '/' + parse + '/' + parse_2, function() {
                $('div.loaderDiv').addClass('d-none');
            });
        } else {
            $("div#content").load('<?= PV::BASE_URL ?><?= $data["controller"] ?>/' + page + '/' + parse, function() {
                $('div.loaderDiv').addClass('d-none');
            });
        }
    }
</script>