<?php
if (setting('pf_im_node_server')) {
    $this->call('$Core_IM.start_im();');
}