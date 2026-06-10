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
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
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
    var appBaseUrl = '<?= PV::BASE_URL ?>';
    var appController = '<?= $data["controller"] ?>';
    var appPage = "<?= isset($data['page']) && $data['page'] != "" ? $data['page'] : 'content' ?>";
    var appParse = '<?= $data['parse'] ?>';
    var appParse2 = '<?= isset($data['parse_2']) ? $data['parse_2'] : '' ?>';

    $(document).ready(function() {
        content();
    });

    function appContentUrl(controller, page, parse, parse2) {
        var url = appBaseUrl + controller + '/' + page;
        if (parse !== '' && parse !== undefined) {
            url += '/' + parse;
            if (parse2 !== '' && parse2 !== undefined) {
                url += '/' + parse2;
            }
        }
        return url;
    }

    function loadAppContent(url, done) {
        $('div.loaderDiv').removeClass('d-none');
        $("div#content").load(url, function() {
            if ($("#content").find("[data-custom-loader]").length == 0) {
                $('div.loaderDiv').addClass('d-none');
            }
            if (typeof done === 'function') {
                done();
            }
        });
    }

    function content(new_parse, new_parse_2, new_controller) {
        if (new_controller) {
            appController = new_controller;
        }
        if (new_parse !== undefined && new_parse !== "") {
            appParse = new_parse;
        }
        if (new_parse_2 !== undefined && new_parse_2 !== "") {
            appParse2 = new_parse_2;
        } else if (new_parse !== undefined && new_parse !== "") {
            appParse2 = '';
        }

        var url = appContentUrl(appController, appPage, appParse, appParse2);
        loadAppContent(url);
    }

    function appNavigateFromHref(href) {
        var path = href.replace(appBaseUrl, '').replace(/^\/+/, '');
        var parts = path.split('/').filter(function(p) {
            return p !== '';
        });
        if (parts.length === 0) {
            return false;
        }

        var controller = parts[0];
        var page = 'content';
        var parse = '';
        var parse2 = '';

        if (parts.length === 1) {
            parse = '';
        } else if (parts[1] === 'index') {
            parse = parts[2] || '';
            parse2 = parts[3] || '';
        } else {
            return false;
        }

        appController = controller;
        appPage = page;
        appParse = parse;
        appParse2 = parse2;
        loadAppContent(appContentUrl(controller, page, parse, parse2));
        if (window.history && window.history.pushState) {
            window.history.pushState({
                href: href
            }, '', href);
        }
        return true;
    }
</script>