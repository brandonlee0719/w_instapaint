<?php
\Core\Queue\Manager::instance()->addHandler('core_email_queue', 'Core_Job_MailQueue');