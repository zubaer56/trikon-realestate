<?php

namespace Drupal\scrollrevealjs_ui;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\PagerSelectExtender;
use Drupal\Core\Database\Query\TableSortExtender;

/**
 * ScrollReveal manager.
 */
class ScrollRevealManager implements ScrollRevealManagerInterface {

  /**
   * The database connection used to check the target against.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a ScrollRevealManager object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection which will be used to check the target
   *   against.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function isScrollReveal($target) {
    return (bool) $this->connection->query("SELECT * FROM {scrollreveal} WHERE [target] = :target", [':target' => $target])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function loadScrollReveal() {
    $query = $this->connection
      ->select('scrollreveal', 's')
      ->fields('s', ['sid', 'target', 'options'])
      ->condition('status', 1);

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function addScrollReveal($sid, $target, $label, $comment, $changed, $status, $options) {
    $this->connection->merge('scrollreveal')
      ->key('sid', $sid)
      ->fields([
        'target'  => $target,
        'label'   => $label,
        'comment' => $comment,
        'changed' => $changed,
        'status'  => $status,
        'options' => $options,
      ])
      ->execute();

    return $this->connection->lastInsertId();
  }

  /**
   * {@inheritdoc}
   */
  public function removeScrollReveal($sid) {
    $this->connection->delete('scrollreveal')
      ->condition('sid', $sid)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function findAll($header = [], $search = '', $status = NULL) {
    $query = $this->connection
      ->select('scrollreveal', 's')
      ->extend(PagerSelectExtender::class)
      ->extend(TableSortExtender::class)
      ->orderByHeader($header)
      ->limit(50)
      ->fields('s');

    if (!empty($search) && !empty(trim((string) $search)) && $search !== NULL) {
      $search = trim((string) $search);
      // Escape for LIKE matching.
      $search = $this->connection->escapeLike($search);
      // Replace wildcards with MySQL/PostgreSQL wildcards.
      $search = preg_replace('!\*+!', '%', $search);
      // Add target and the label field columns.
      $group = $query->orConditionGroup()
        ->condition('target', '%' . $search . '%', 'LIKE')
        ->condition('label', '%' . $search . '%', 'LIKE');
      // Run the query to find matching targets.
      $query->condition($group);
    }

    // Check if status is set.
    if (!is_null($status) && $status != '') {
      $query->condition('status', $status);
    }

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function findById($sid) {
    return $this->connection->query("SELECT [target], [label], [comment], [status], [options] FROM {scrollreveal} WHERE [sid] = :sid", [':sid' => $sid])
      ->fetchAssoc();
  }

}
