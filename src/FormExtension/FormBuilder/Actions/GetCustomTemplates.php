<?php

namespace GivePdfReceipt\FormExtension\FormBuilder\Actions;

/**
 * Get the custom templates created by the user (using as base the starter templates) on PDF Builder > Custom PDF Builder
 *
 * @since 3.1.0
 */
class GetCustomTemplates
{
    /**
     * @since 3.1.0
     */
    public function __invoke(): array
    {
        // Get customized templates
        $posts = get_posts(apply_filters('give_custom_pdf_receipts_templates_query_args', [
                'post_type' => 'give_pdf_template',
                'post_status' => ['draft', 'publish'],
                'posts_per_page' => -1,
                'meta_query' => [
                    // Here for backwards compatibility when we used to save templates to the CPT.
                    [
                        'key' => '_give_pdf_receipts_template',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ]
        ));

        $customTemplates = [];
        foreach ($posts as $post) {
            $customTemplates[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'nonce' => wp_create_nonce("give_can_edit_template_{$post->ID}"),
            ];
        }

        return $customTemplates;
    }
}
