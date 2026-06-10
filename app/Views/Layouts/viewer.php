<style>
    .disabled_all {
        pointer-events: none;
        opacity: 0.8;
    }

    #content.content-is-loading {
        position: relative;
        min-height: 320px;
    }

    #content .content-loader {
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(2px);
        border-radius: 0.35rem;
    }

    .content-loader-card {
        text-align: center;
        padding: 1.25rem 1.75rem;
        border-radius: 0.65rem;
        background: #fff;
        box-shadow: 0 0.35rem 1.25rem rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .content-loader-dots {
        display: flex;
        justify-content: center;
        gap: 0.45rem;
        margin-bottom: 0.65rem;
    }

    .content-loader-dots span {
        width: 0.55rem;
        height: 0.55rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #04871e, #3193df);
        animation: content-loader-bounce 1s ease-in-out infinite;
    }

    .content-loader-dots span:nth-child(2) {
        animation-delay: 0.15s;
    }

    .content-loader-dots span:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes content-loader-bounce {
        0%, 80%, 100% {
            transform: translateY(0);
            opacity: 0.45;
        }

        40% {
            transform: translateY(-7px);
            opacity: 1;
        }
    }
</style>

<script>
    var appBaseUrl = '<?= PV::BASE_URL ?>';
    var appController = '<?= $data["controller"] ?>';
    var appPage = "<?= isset($data['page']) && $data['page'] != "" ? $data['page'] : 'content' ?>";
    var appParse = '<?= $data['parse'] ?>';
    var appParse2 = '<?= isset($data['parse_2']) ? $data['parse_2'] : '' ?>';

    function contentLoaderMarkup() {
        return '<div class="content-loader" aria-live="polite" aria-busy="true">' +
            '<div class="content-loader-card">' +
            '<div class="content-loader-dots"><span></span><span></span><span></span></div>' +
            '<div class="small text-muted fw-bold">Memuat halaman...</div>' +
            '</div></div>';
    }

    function showContentLoader() {
        var $content = $('#content');
        $content.addClass('content-is-loading');
        if ($content.children('.content-loader').length === 0) {
            $content.append(contentLoaderMarkup());
        } else {
            $content.children('.content-loader').removeClass('d-none').attr('aria-busy', 'true');
        }
    }

    function hideContentLoader() {
        var $content = $('#content');
        $content.removeClass('content-is-loading');
        $content.children('.content-loader').addClass('d-none').attr('aria-busy', 'false');
    }

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
        showContentLoader();
        $("div#content").load(url, function(response, status) {
            if (status === 'error') {
                hideContentLoader();
                $('#content').html('<div class="alert alert-danger m-3">Gagal memuat halaman. <a href="javascript:location.reload()">Coba lagi</a></div>');
            } else if ($("#content").find("[data-custom-loader]").length == 0) {
                hideContentLoader();
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
