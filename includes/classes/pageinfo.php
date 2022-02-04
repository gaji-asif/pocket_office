<?php

class pageinfo {
    public $title;
    public $source;
    public $raw_icon;
    public $sm_icon;
    public $lg_icon;

    private $navigation_id;
    private $order_num;

    function __construct($src)
    {
        if (!UserModel::loggedIn()) {
            parentRedirect(ACCOUNT_URL);
        }

        $sql = "select navigation_id, title, icon, order_num from navigation where source='" . $src . "' limit 1";
        $res = DBUtil::query($sql);
        if (mysqli_num_rows($res) == 0)
            die("<b>Invalid Page Reference</b>");
        list($this->navigation_id, $this->title, $this->raw_icon, $this->order_num) = mysqli_fetch_row($res);

        $this->source = $src;
        $this->sm_icon = "images/icons/" . $this->raw_icon . "_16.png";
        $this->lg_icon = "images/icons/" . $this->raw_icon . "_32.png";
    }

    public function getHeader($notes = FALSE) {
        $title = str_replace('System', '<a href="/system.php">System</a>', $this->title);
        $header = '';
        if($notes) {
            $header = '<span id="notes"></span>';
        }
        return $header . "<h1 class=\"page-title\"><i class=\"icon-{$this->raw_icon}\"></i>$title</h1>";
    }
}