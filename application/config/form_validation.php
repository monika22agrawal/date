<?php
/* define set of rules for a group */
$config  = array(
    //rules for category
    'category' => array(
        array(
            'field' => 'cat_name',
            'label' => 'Category Name',
            'rules' => 'required|trim|min_length[3]|max_length[200]|callback__check_unique[categories.name]|strip_tags',
        ),
        array(
            'field' => 'cat_desc',
            'label' => 'Category Description',
            'rules' => 'trim|alpha_numeric|max_length[200]|strip_tags'
        ),
    )
    
    
);