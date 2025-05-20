<?php
function render_button($button) {
    if (!is_object($button)) {
        return '<button class="btn btn-primary">Invalid Button</button>';
    }
    if (isset($button->text)) {
        $button->text = sanitize_text_field($button->text);
    } else {
        return '<button class="btn btn-primary">Invalid Button</button>';
    }
    if (isset($button->url)) {
        $button->url = esc_url($button->url);
    }
    $button = (object) $button;
    $classes = 'btn btn-primary text-white bg-blue-500 p-4 m-4 font-bold btn-primary';
    if (isset($button->class)) {
        $classes .= ' ' . esc_attr($button->class);
    }
    if (isset($button->icon)) {
        $icon = '<i class="' . esc_attr($button->icon) . '"></i>';
    } else {
        $icon = '';
    }
    if (isset($button->url)) {
        $url = esc_url($button->url);
        return '<a href="' . $url . '" class="' . $classes . '">' . $icon . esc_html($button->text) . '</a>';
    } else {
        return '<button class="' . $classes . '">' . $icon . esc_html($button->text) . '</button>';
    }
}