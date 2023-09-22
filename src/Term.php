<?php
namespace Cvy\WP\Term;

use \Cvy\WP\PostsQuery\PostsQuery;

class Term extends \Cvy\WP\Object\WPObject
{
  /**
   * @return string Parent taxonomy slug.
   */
  public function get_taxonomy_slug() : string
  {
    return $this->get_original()->taxonomy;
  }

  /**
   * @return string Term name.
   */
  public function get_label() : string
  {
    return $this->get_original()->name;
  }

  // @todo: major; make final
  protected function get_acf_id() : string
  {
    return $this->get_taxonomy_slug() . '_' . $this->get_id();
  }

  /**
   * WP original term instance.
   *
   * @return \WP_Term
   */
  public function get_original() : \WP_Term
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

  /**
   * Retrieves posts under current term.
   *
   * @param PostsQuery $posts_query Posts query instance (MUST NOT be executed yet).
   * @return array Posts IDs.
   */
  public function get_posts( PostsQuery $posts_query = null ) : array
  {
    if ( ! $posts_query )
    {
      $posts_query = new PostsQuery([
        'post_type' => 'any',
      ]);
    }

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
