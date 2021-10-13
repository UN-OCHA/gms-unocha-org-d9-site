<?php

namespace Drupal\gms_pdflink\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a block with a link to print entities.
 *
 * @Block(
 *   id = "print_links",
 *   admin_label = @Translation("Print Links"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity:node")
 *   },
 * )
 */
class PrintLinks extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routematch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $routematch, EntityTypeManager $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routematch = $routematch;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $settings = $form_state->getValue('cache');
    $this->configuration['max_age'] = $settings['max_age'];
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = $this->getContextValue('entity');
    $markup = "<a href=\"/page/print/pdf/node/".$entity->id()."\" target=\"_blank\">".$this->t('Download this Page')."</a>";
    return [
      '#markup' => Markup::create($markup . "\n"),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }
}
