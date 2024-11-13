<script>
    $(document).ready(function() {
        content();
    });

    function content() {
        $("div#content").load('<?= $this->BASE_URL ?><?= $data['page'] ?>/content/<?= $data['parse'] ?>/<?= $data['parse_2'] ?>');
    }
</script>