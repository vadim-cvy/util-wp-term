<?php
namespace Cvy\WP\Term;

use \Cvy\WP\PostsQuery\PostsQuery;
use \Cvy\WP\PostsQuery\PostsQueryArgs;

abstract class Term extends \Cvy\WP\Object\WPObject
{
  protected $taxonomy_slug;

  public function __construct( int $id, string $taxonomy_slug = null )
  {
    parent::__construct( $id );

    $this->taxonomy_slug = $taxonomy_slug;
  }

  public function get_taxonomy_slug() : string
  {
    if ( ! $this->taxonomy_slug )
    {
      throw new \Exception( 'Taxonomy slug is not set!' );
    }

    return $this->taxonomy_slug;
  }

  public function get_label() : string
  {
    return $this->get_original()->name;
  }

  protected function get_acf_id() : string
  {
    return $this->get_taxonomy_slug() . '_' . $this->get_id();
  }

  public function get_original() : object
  {
    return get_term( $this->get_id(), $this->get_taxonomy_slug() );
  }

  public function get_meta( string $selector )
  {
    return get_term_meta( $this->get_id(), $selector, true );
  }

  public function update_meta( string $selector, $value ) : void
  {
    update_term_meta( $this->get_id(), $selector, $value );
  }

  public function delete_meta( string $selector ) : void
  {
    delete_term_meta( $this->get_id(), $selector );
  }

  public function get_posts( PostsQuery $posts_query ) : array
  {
    $posts_query->patch([
      'tax_query' => [[
        'taxonomy' => $this->get_taxonomy_slug(),
        'field' => 'term_id',
        'terms' => [ $this->get_id() ],
      ]],
    ]);

    return $posts_query->get_results();
  }
}
