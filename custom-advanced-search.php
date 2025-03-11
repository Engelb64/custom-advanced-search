<?php
/*
Plugin Name: Custom Advanced Search
Description: Plugin, Buscador de prueba para filtros por categoría, paginas y archivos adjuntos en la pagina de resultados en wordpress.
Version: 1.0
Author: Engelbertg J Bracho R
*/

/**
 * Modificacion de las consultas de busqueda para aplicar los filtros seleccionados
 * */
add_action('pre_get_posts', function ($query) {
    if ($query->is_search && $query->is_main_query()) {
        $post_types = [];

        if (!empty($_GET['search_pages'])) {
            $post_types[] = 'post'; 
        }
        if (!empty($_GET['search_attachments'])) {
            $post_types[] = 'attachment'; 
        }

        if (empty($post_types)) {
            $post_types = ['post', 'page', 'attachment'];
        }

        $query->set('post_type', $post_types);
        $query->set('post_status', ['inherit', 'publish']); 
        $query->set('posts_per_page', 15);

        if (!empty($_GET['category'])) {
            $query->set('cat', intval($_GET['category'])); 
        }
    }
});

/**
 *  Mostrar el formulario de busqueda con filtros en la pagina de resultados
 */
add_action('loop_start', function ($query) {
    if (is_search() && $query->is_main_query()) {
        $search_pages_checked = !empty($_GET['search_pages']) ? 'checked' : '';
        $search_attachments_checked = !empty($_GET['search_attachments']) ? 'checked' : '';
        $selected_category = !empty($_GET['category']) ? intval($_GET['category']) : '';

        $categories = get_categories([
            'hide_empty' => true,
        ]);

        echo '<br>
            <form role="search" method="get" id="searchform" action="' . home_url('/') . '">
                <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Buscar...">
                <div>
                    <label>
                        <input type="checkbox" name="search_pages" value="1" ' . $search_pages_checked . '>
                        Buscar en artículos
                    </label>
                    <label>
                        <input type="checkbox" name="search_attachments" value="1" ' . $search_attachments_checked . '>
                        Buscar en archivos
                    </label>
                </div>
                <div>
                    <label for="category">Categoría del articulo:</label>
                    <select name="category" id="category">
                        <option value="">Todas las categorías</option>';
        foreach ($categories as $category) {
            echo '<option value="' . $category->term_id . '"' . selected($selected_category, $category->term_id, false) . '>' . $category->name . '</option>';
        }
        echo        '</select>
                </div>
                <input type="submit" id="searchsubmit" value="Buscar">
            </form>';
    }
});

/**
 * Shortcode para uso en la pagina [custom_search_form]
 */
function custom_search_form()
{
    return '
        <form role="search" method="get" id="searchform" action="' . home_url('/') . '">
            <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Buscar...">
            <input type="submit" id="searchsubmit" value="Buscar">
        </form>';
}

add_shortcode('custom_search_form', 'custom_search_form');
