<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Exception;
use Doctrine\ORM\EntityManager;
use Language;
use Newscoop\View\ArticleView;

/**
 * Article Indexer
 */
class ArticleIndexer
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Newscoop\Search\Index
     */
    private $index;

    /**
     * @var array
     */
    private $config = array(
        'additional_indexable_fields' => array()
    );

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Search\Index $index
     * @param array $config
     */
    public function __construct(EntityManager $em, Index $index, array $config)
    {
        $this->em = $em;
        $this->index = $index;
        $this->config = (object) array_merge($this->config, $config);
    }

    /**
     * Update index to reflect article changes
     *
     * @param int $limit
     * @return void
     */
    public function updateIndex($limit = 50)
    {
        $articles = $this->getArticleRepository()->getIndexBatch($limit);
        foreach ($articles as $article) {

            $articleView = $this->prepareArticleForSolr($article);

            $this->getArticleRepository()->setArticleIndexedNow($article);
            if ($articleView->published !== null) {
                $this->index->add($articleView);
            } elseif ($articleView->number) {
                $this->index->delete($articleView);
            }
        }

        $this->index->commit();

        return count($articles);
    }

    /**
     * Reset index
     *
     * @return void
     */
    public function reset()
    {
        $this->getArticleRepository()->resetIndex();
    }

    /**
     * Handle article.delete event
     *
     * @param Newscoop\Event
     * @return void
     */
    public function update($event)
    {
        $article = $event->getSubject();
        $language = new Language($article->getLanguageId());

        $this->index->delete(
            new ArticleView(
                array(
                    'number' => $article->getArticleNumber(),
                    'language' => $language->getCode(),
                )
            )
        );

        try {
            $this->index->commit();
        } catch (Exception $e) {
            // ignore
        }
    }

    /**
     * Prepares the article for indexing by Solr. It creates an articleView and
     * also changes the names of articleType specific fields that need to be
     * indexeable by Solr. These fiels can be set in the configuration.
     *
     * @param  Newscoop\Entity\Article $article
     *
     * @return Newscoop\View\ArticleView Returns an  ArticleView of the Article
     */
    private function prepareArticleForSolr($article)
    {
        $fieldTypes = $article->getArticleDataFieldTypes();
        $articleView = $article->getView();

        foreach ($articleView AS $property => $value) {
            if (!in_array($property, $this->config->additional_indexable_fields)) {
                continue;
            }
            if (!array_key_exists($property, $fieldTypes)) {
                continue;
            }

            switch($fieldTypes[$property]) {
                case 'string':
                    $newProperty = $property.'_text';
                break;
                case 'integer':
                    $newProperty = $property.'_int';
                break;
                case 'date':
                    $newProperty = $property.'_date';
                break;
                default:
                    continue;
                break;
            }

            $articleView->$newProperty = $articleView->$property;
            unset($articleView->$property);
        }

        return $articleView;
    }

    /**
     * Get article repository
     *
     * @return Newscoop\Entity\Repository\ArticleRepository
     */
    private function getArticleRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Article');
    }
}
