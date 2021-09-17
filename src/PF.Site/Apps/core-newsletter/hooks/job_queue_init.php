<?php
\Core\Queue\Manager::instance()->addHandler('core_newsletter_send_email_users',
    '\Apps\Core_Newsletter\Job\SendEmailUsers');
