<?php

if (!function_exists('simple_pagination')) {
    /**
     * Generate simple pagination HTML
     */
    function simple_pagination($pager, $options = [])
    {
        // Jika pager sudah berupa string HTML, langsung return
        if (is_string($pager)) {
            return $pager;
        }
        
        // Jika pager bukan object atau tidak punya method links
        if (!is_object($pager) || !method_exists($pager, 'links')) {
            return '';
        }
        
        $defaultOptions = [
            'show_first_last' => false,
            'show_numbers' => true,
            'ul_class' => 'pagination justify-content-center',
            'li_class' => 'page-item',
            'active_class' => 'active',
            'disabled_class' => 'disabled',
            'link_class' => 'page-link',
            'previous_text' => '&laquo;',
            'next_text' => '&raquo;',
            'first_text' => '&laquo;&laquo;',
            'last_text' => '&raquo;&raquo;'
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '<nav aria-label="Page navigation">';
        $html .= '<ul class="' . $options['ul_class'] . '">';
        
        // Previous link
        if ($pager->hasPrevious()) {
            $html .= '<li class="' . $options['li_class'] . '">';
            $html .= '<a class="' . $options['link_class'] . '" href="' . $pager->getPrevious() . '" aria-label="Previous">';
            $html .= '<span aria-hidden="true">' . $options['previous_text'] . '</span>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="' . $options['li_class'] . ' ' . $options['disabled_class'] . '">';
            $html .= '<span class="' . $options['link_class'] . '">' . $options['previous_text'] . '</span>';
            $html .= '</li>';
        }
        
        // Page numbers
        if ($options['show_numbers']) {
            $links = $pager->links();
            if (is_array($links)) {
                foreach ($links as $link) {
                    $active = isset($link['active']) && $link['active'] ? ' ' . $options['active_class'] : '';
                    $uri = isset($link['uri']) ? $link['uri'] : '#';
                    $title = isset($link['title']) ? $link['title'] : '';
                    
                    $html .= '<li class="' . $options['li_class'] . $active . '">';
                    $html .= '<a class="' . $options['link_class'] . '" href="' . $uri . '">';
                    $html .= $title;
                    $html .= '</a></li>';
                }
            }
        }
        
        // Next link
        if ($pager->hasNext()) {
            $html .= '<li class="' . $options['li_class'] . '">';
            $html .= '<a class="' . $options['link_class'] . '" href="' . $pager->getNext() . '" aria-label="Next">';
            $html .= '<span aria-hidden="true">' . $options['next_text'] . '</span>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="' . $options['li_class'] . ' ' . $options['disabled_class'] . '">';
            $html .= '<span class="' . $options['link_class'] . '">' . $options['next_text'] . '</span>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</nav>';
        
        return $html;
    }
}

if (!function_exists('get_pagination_info')) {
    /**
     * Get pagination information
     */
    function get_pagination_info($pager)
    {
        if (is_string($pager)) {
            return [
                'current_page' => 1,
                'total_pages' => 1,
                'total_items' => 0,
                'per_page' => 10
            ];
        }
        
        if (is_object($pager) && method_exists($pager, 'getDetails')) {
            return $pager->getDetails();
        }
        
        return [];
    }
}

if (!function_exists('bootstrap_pagination')) {
    /**
     * Bootstrap 5 pagination
     */
    function bootstrap_pagination($pager)
    {
        if (is_string($pager)) {
            return $pager;
        }
        
        if (!is_object($pager) || !method_exists($pager, 'links')) {
            return '';
        }
        
        $links = $pager->links();
        if (is_string($links)) {
            return $links;
        }
        
        $html = '<nav aria-label="Page navigation">';
        $html .= '<ul class="pagination justify-content-center">';
        
        // Previous
        if ($pager->hasPrevious()) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $pager->getPrevious() . '">&laquo;</a>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link">&laquo;</span>';
            $html .= '</li>';
        }
        
        // Page numbers
        foreach ($pager->links() as $link) {
            $active = $link['active'] ? ' active' : '';
            $html .= '<li class="page-item' . $active . '">';
            $html .= '<a class="page-link" href="' . $link['uri'] . '">' . $link['title'] . '</a>';
            $html .= '</li>';
        }
        
        // Next
        if ($pager->hasNext()) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . $pager->getNext() . '">&raquo;</a>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<span class="page-link">&raquo;</span>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</nav>';
        
        return $html;
    }
}