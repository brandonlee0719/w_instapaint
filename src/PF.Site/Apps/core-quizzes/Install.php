<?php
namespace Apps\Core_Quizzes;

use Core\App;
use Phpfox;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\Core_Quizzes
 */
class Install extends App\App
{
    public $store_id = 1906;
    private $_app_phrases = [
    ];

    protected function setId()
    {
        $this->id = 'Core_Quizzes';
    }

    protected function setAlias()
    {
        $this->alias = 'quiz';
    }

    protected function setName()
    {
        $this->name = _p('Quizzes');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '';
    }

    protected function setSettings()
    {
        $this->settings = [
            'default_answers_count' => [
                'var_name' => 'default_answers_count',
                'info' => 'How Many Answers Per Default',
                'description' => 'When adding a new question in a quiz how many answer fields to show',
                'type' => 'integer',
                'value' => '4',
                'ordering' => 1,
            ],
            'show_percentage_in_track' => [
                'var_name' => 'show_percentage_in_track',
                'info' => 'Show success as percentage in Tracker',
                'description' => 'In the block "Recently Taken By" set this to true if you want the success of each user to be shown as a percentage: 75%<br/>If you set it to false it will be shown as correct vs total answers: 3/4',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 2,
            ],
            'show_percentage_in_results' => [
                'var_name' => 'show_percentage_in_results',
                'info' => 'Show success as percentage in Results',
                'description' => 'When viewing "Users Results" if you set this to true you will see results as a percentage: 75%<br/>If you set it to false you will see results as correct vs total: 3/4',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 3,
            ],
            'quiz_paging_mode' => [
                'var_name' => 'quiz_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Pre buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => 4
            ],
        ];
        if (!Phpfox::getParam('quiz.quiz_meta_description', null)) {
            $this->settings['quiz_meta_description'] = [
                'var_name' => 'quiz_meta_description',
                'info' => 'Quiz Meta Description',
                'description' => 'Meta description added to pages related to the Quizzes app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=quiz_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="quiz_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_quiz_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => 5,
            ];
        }
        if (!Phpfox::getParam('quiz.quiz_meta_keywords', null)) {
            $this->settings['quiz_meta_keywords'] = [
                'var_name' => 'quiz_meta_keywords',
                'info' => 'Quiz Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Quizzes app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=quiz_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="quiz_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_quiz_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => 6,
            ];
        }
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'max_questions' => [
                'var_name' => 'max_questions',
                'info' => 'How many questions can a new Quiz (created by a member of this user group) have.',
                'description' => 'For unlimited add "0" without quotes',
                'type' => 'integer',
                'value' => [
                    '1' => '9999',
                    '2' => '10',
                    '3' => '0',
                    '4' => '10',
                    '5' => '10'
                ],
                'ordering' => 1,
            ],
            'min_questions' => [
                'var_name' => 'min_questions',
                'info' => 'How many questions is the least a Quiz (created by members of this user group) can have',
                'description' => 'Minimum value is 1',
                'type' => 'integer',
                'value' => [
                    '1' => '1',
                    '2' => '5',
                    '3' => '9999',
                    '4' => '2',
                    '5' => '0'
                ],
                'ordering' => 2,
            ],
            'max_answers' => [
                'var_name' => 'max_answers',
                'info' => 'How many answers (maximum) can each question in a quiz have?',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    '1' => '25',
                    '2' => '10',
                    '3' => '1',
                    '4' => '15',
                    '5' => '0'
                ],
                'ordering' => 3,
            ],
            'min_answers' => [
                'var_name' => 'min_answers',
                'info' => 'How many answers (minimum) can a question in a quiz have?',
                'description' => 'Minimum value is 2.',
                'type' => 'integer',
                'value' => [
                    '1' => '2',
                    '2' => '2',
                    '3' => '9999',
                    '4' => '2',
                    '5' => '0'
                ],
                'ordering' => 4,
            ],
            'can_answer_own_quiz' => [
                'var_name' => 'can_answer_own_quiz',
                'info' => 'Can users answer their own quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 5,
            ],
            'can_approve_quizzes' => [
                'var_name' => 'can_approve_quizzes',
                'info' => 'Can approve quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 6,
            ],
            'can_delete_own_quiz' => [
                'var_name' => 'can_delete_own_quiz',
                'info' => 'Can delete their own quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 7,
            ],
            'can_delete_others_quizzes' => [
                'var_name' => 'can_delete_others_quizzes',
                'info' => 'Can delete all quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 8,
            ],
            'new_quizzes_need_moderation' => [
                'var_name' => 'new_quizzes_need_moderation',
                'info' => 'Approve quizzes before they are publicly displayed?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 9,
            ],
            'can_post_comment_on_quiz' => [
                'var_name' => 'can_post_comment_on_quiz',
                'info' => 'Can post comments on quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 10,
            ],
            'can_edit_own_questions' => [
                'var_name' => 'can_edit_own_questions',
                'info' => 'Can edit their own quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 11,
            ],
            'can_edit_others_questions' => [
                'var_name' => 'can_edit_others_questions',
                'info' => 'Can edit all quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 12,
            ],
            'can_view_results_before_answering' => [
                'var_name' => 'can_view_results_before_answering',
                'info' => 'If this option is enabled members of this user group will be able to view what other users answered in a quiz before they answer the quiz themselves.',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 13,
            ],
            'can_upload_picture' => [
                'var_name' => 'can_upload_picture',
                'info' => 'Can members of this user group upload a picture along with the quiz?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 14,
            ],
            'is_picture_upload_required' => [
                'var_name' => 'is_picture_upload_required',
                'info' => 'Is it a requirement to upload a with the quiz? ',
                'description' => 'Be careful as this setting along with the "Can members of this user group upload a picture along with the quiz?" could keep members from uploading any quiz (this setting enabled but below setting disabled would render a useless add quiz page because of the mutual exclusion)',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 15,
            ],
            'can_access_quiz' => [
                'var_name' => 'can_access_quiz',
                'info' => 'Can browse and view the quiz module?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 16,
            ],
            'can_create_quiz' => [
                'var_name' => 'can_create_quiz',
                'info' => 'Can create a quiz?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 17,
            ],
            'points_quiz' => [
                'var_name' => 'points_quiz',
                'info' => 'Activity points',
                'description' => 'Specify how many points the user will receive when adding a new quiz.',
                'type' => 'integer',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 18,
            ],
            'can_feature_quiz' => [
                'var_name' => 'can_feature_quiz',
                'info' => 'Can feature quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 19,
            ],
            'can_sponsor_quiz' => [
                'var_name' => 'Can mark a quiz as sponsor?',
                'info' => 'Can members of this user group mark a quiz as Sponsor without paying fee?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 20,
            ],
            'can_purchase_sponsor_quiz' => [
                'var_name' => 'can_purchase_sponsor_quiz',
                'info' => 'Can members of this user group purchase a sponsored ad space for their quizzes?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 21,
            ],
            'quiz_sponsor_price' => [
                'var_name' => 'quiz_sponsor_price',
                'info' => 'How much is the sponsor space worth for quizzes? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'ordering' => 22,
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info' => 'Auto publish sponsored item?',
                'description' => 'After the user has purchased a sponsored space, should the item be published right away? 
If set to No, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 23
            ],
            'quiz_max_upload_size' => [
                'var_name' => 'quiz_max_upload_size',
                'info' => 'Max file size for quiz photos upload',
                'description' => 'Max file size for quiz photos upload in kilobits (kb). (1000 kb = 1 mb) 
For unlimited add "0" without quotes.',
                'type' => 'integer',
                'value' => [
                    '1' => '5000',
                    '2' => '5000',
                    '3' => '5000',
                    '4' => '5000',
                    '5' => '5000'
                ],
                'ordering' => 24
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'featured' => '',
                'sponsored' => '',
                'stat' => '',
            ],
            'controller' => [
                'index' => 'quiz.index',
                'view' => 'quiz.view',
                'profile' => 'quiz.profile',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Featured' => [
                'type_id' => '0',
                'm_connection' => 'quiz.index',
                'component' => 'featured',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Sponsored' => [
                'type_id' => '0',
                'm_connection' => 'quiz.index',
                'component' => 'sponsored',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
            'Recently Taken' => [
                'type_id' => '0',
                'm_connection' => 'quiz.view',
                'component' => 'stat',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '1',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_quiz',
            'url' => 'quiz',
            'icon' => 'puzzle-piece'
        ];
        $this->database = [
            'Quiz',
            'Quiz_Answer',
            'Quiz_Question',
            'Quiz_Result'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/quiz/'
        ];
        $this->admincp_route = Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'Core_Quizzes']);;
        $this->_apps_dir = 'core-quizzes';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}