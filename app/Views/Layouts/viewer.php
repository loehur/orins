<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        content();
    });

    function content(new_parse = "", new_parse_2 = "") {
        if (new_parse != "") {
            parse = new_parse
        } else {
            parse = '<?= $data['parse'] ?>';
        }

        if (new_parse_2 != "") {
            parse_2 = new_parse_2
        } else {
            parse_2 = '<?= $data['parse_2'] ?>';
        }
        page = "<?= isset($data['page']) ? $data['page'] : 'content' ?>";
        $("div#content").load('<?= $this->BASE_URL ?><?= $data["controller"] ?>/' + page + '/' + parse + '/' + parse_2);
    }
</script>