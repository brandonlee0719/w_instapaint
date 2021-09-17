<?php

\Core\Queue\Manager::instance()->addHandler('item_liked', 'Like_Job_ItemLiked');
