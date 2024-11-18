<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        content();
    });

    function content(new_parse = "") {
        if (new_parse != "") {
            parse = new_parse
        } else {
            parse = '<?= $data['parse'] ?>';
        }
        page = "<?= isset($data['page']) ? $data['page'] : 'content' ?>";
        $("div#content").load('<?= $this->BASE_URL ?><?= $data["controller"] ?>/' + page + '/' + parse);
    }
</script>