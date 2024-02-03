<?php

namespace Drupal\scrollrevealjs_ui;

/**
 * Provides an interface defining an ScrollReveal target manager.
 */
interface ScrollRevealManagerInterface {

  /**
   * Returns if this ScrollReveal target is added.
   *
   * @param string $target
   *   The ScrollReveal css selector to check.
   *
   * @return bool
   *   TRUE if the ScrollReveal js target is added, FALSE otherwise.
   */
  public function isScrollReveal($target);

  /**
   * Finds all enabled ScrollReveal records.
   *
   * @return string|false
   *   Either the enabled ScrollReveal target or FALSE.
   */
  public function loadScrollReveal();

  /**
   * Add a ScrollReveal target.
   *
   * @param int $sid
   *   The ScrollReveal id for edit.
   * @param string $target
   *   The ScrollReveal selector to add.
   * @param string $label
   *   The label of ScrollReveal target.
   * @param string $comment
   *   The comment for ScrollReveal options.
   * @param int $changed
   *   The expected modification time.
   * @param int $status
   *   The status for ScrollReveal.
   * @param string $options
   *   The ScrollReveal target options.
   *
   * @return int|null|string
   *   The last insert ID of the query, if one exists.
   */
  public function addScrollReveal($sid, $target, $label, $comment, $changed, $status, $options);

  /**
   * Remove a ScrollReveal target.
   *
   * @param int $sid
   *   The ScrollReveal id to remove.
   */
  public function removeScrollReveal($sid);

  /**
   * Finds all added ScrollReveal targets.
   *
   * @param array $header
   *   The ScrollReveal header to sort target and label.
   * @param string $search
   *   The ScrollReveal search key to filter target.
   * @param int|null $status
   *   The ScrollReveal status to filter target.
   *
   * @return \Drupal\Core\Database\StatementInterface
   *   The result of the database query.
   */
  public function findAll($header, $search, $status);

  /**
   * Finds an added ScrollReveal js target by its ID.
   *
   * @param int $sid
   *   The ID for an added ScrollReveal target.
   *
   * @return string|false
   *   Either the added ScrollReveal selector or FALSE
   *   if none exist with that ID.
   */
  public function findById($sid);

}
