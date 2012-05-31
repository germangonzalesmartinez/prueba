<?php

/**
 * @file tests/classes/search/ArticleSearchTest.inc.php
 *
 * Copyright (c) 2000-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ArticleSearchTest
 * @ingroup tests_classes_search
 * @see ArticleSearch
 *
 * @brief Test class for the ArticleSearch class
 */


require_mock_env('env1');

import('lib.pkp.tests.PKPTestCase');
import('lib.pkp.classes.core.ArrayItemIterator');
import('lib.pkp.classes.plugins.HookRegistry'); // This is the HookRegistry from mock env1.
import('classes.search.ArticleSearch');

define('ARTICLE_SEARCH_TEST_DEFAULT_ARTICLE', 1);
define('ARTICLE_SEARCH_TEST_ARTICLE_FROM_PLUGIN', 2);

class ArticleSearchTest extends PKPTestCase {

	//
	// Implementing protected template methods from PKPTestCase
	//
	/**
	 * @see PKPTestCase::getMockedDAOs()
	 */
	protected function getMockedDAOs() {
		$mockedDaos = parent::getMockedDAOs();
		$mockedDaos += array(
			'ArticleSearchDAO', 'ArticleDAO', 'PublishedArticleDAO',
			'IssueDAO', 'JournalDAO', 'SectionDAO'
		);
		return $mockedDaos;
	}

	/**
	 * @see PKPTestCase::setUp()
	 */
	protected function setUp() {
		parent::setUp();

		// Prepare the mock environment for this test.
		$this->registerMockArticleSearchDAO();
		$this->registerMockArticleDAO();
		$this->registerMockPublishedArticleDAO();
		$this->registerMockIssueDAO();
		$this->registerMockJournalDAO();
		$this->registerMockSectionDAO();
	}

	/**
	 * @see PKPTestCase::tearDown()
	 */
	public function tearDown() {
		HookRegistry::resetCalledHooks();
		parent::tearDown();
	}


	//
	// Unit tests
	//
	/**
	 * @covers ArticleSearch
	 */
	public function testRetrieveResults() {
		// Test a simple search with a mock database back-end.
		$journal = new Journal();
		$keywords = array(
			array('+' => array(array('test')), '' => array(), '-' => array())
		);
		$articleSearch = new ArticleSearch();
		$searchResult = $articleSearch->retrieveResults($journal, $keywords);

		// Test whether the result from the mocked DAOs is being returned.
		self::assertInstanceOf('ItemIterator', $searchResult);
		$firstResult = $searchResult->next();
		self::assertArrayHasKey('article', $firstResult);
		self::assertEquals(ARTICLE_SEARCH_TEST_DEFAULT_ARTICLE, $firstResult['article']->getId());
	}

	/**
	 * @covers ArticleSearch
	 */
	public function testRetrieveResultsViaPluginHook() {
		// Diverting a search to the search plugin hook.
		HookRegistry::register('ArticleSearch::retrieveResults', array($this, 'callbackRetrieveResults'));

		// Test a simple search with the simulated callback.
		$journal = new Journal();
		$keywords = array(
			array('+' => array(array('test')), '' => array(), '-' => array())
		);
		$articleSearch = new ArticleSearch();
		$searchResult = $articleSearch->retrieveResults($journal, $keywords);

		// Test and clear the call history of the hook registry.
		$calledHooks = HookRegistry::getCalledHooks();
		self::assertEquals('ArticleSearch::retrieveResults', $calledHooks[0][0]);
		HookRegistry::clear('ArticleSearch::retrieveResults');

		// Test whether the result from the hook is being returned.
		self::assertInstanceOf('ItemIterator', $searchResult);
		$firstResult = $searchResult->next();
		self::assertArrayHasKey('article', $firstResult);
		self::assertEquals(ARTICLE_SEARCH_TEST_ARTICLE_FROM_PLUGIN, $firstResult['article']->getId());
	}


	//
	// Public callback methods
	//
	/**
	 * Simulate a search plug-ins "retrieve results" hook.
	 * @see ArticleSearch::retrieveResults()
	 */
	public function callbackRetrieveResults($hook, $params) {
		// Mock a result set and return it.
		$mergedResults = array(
			ARTICLE_SEARCH_TEST_ARTICLE_FROM_PLUGIN => 3
		);
		return $mergedResults;
	}

	/**
	 * Callback dealing with ArticleDAO::getArticle()
	 * calls via our mock ArticleDAO.
	 *
	 * @see ArticleDAO::getArticle()
	 */
	public function callbackGetArticle($articleId, $journalId = null, $useCache = false) {
		// Create an article instance with the correct id.
		$article = new Article();
		$article->setId($articleId);
		return $article;
	}


	//
	// Private helper methods
	//
	/**
	 * Mock and register an ArticleSearchDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockArticleSearchDAO() {
		// Mock an ArticleSearchDAO.
		$articleSearchDAO = $this->getMock('ArticleSearchDAO', array('getPhraseResults'), array(), '', false);

		// Mock a result set.
		$searchResult = array(
			array('article_id' => ARTICLE_SEARCH_TEST_DEFAULT_ARTICLE, 'count' => 3)
		);
		$searchResultIterator = new ArrayItemIterator($searchResult);

		// Mock the getPhraseResults() method.
		$articleSearchDAO->expects($this->any())
		                 ->method('getPhraseResults')
		                 ->will($this->returnValue($searchResultIterator));

		// Register the mock DAO.
		DAORegistry::registerDAO('ArticleSearchDAO', $articleSearchDAO);
	}

	/**
	 * Mock and register an ArticleDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockArticleDAO() {
		// Mock an ArticleDAO.
		$articleDAO = $this->getMock('ArticleDAO', array('getArticle'), array(), '', false);

		// Mock an article.
		$article = new Article();

		// Mock the getArticle() method.
		$articleDAO->expects($this->any())
		           ->method('getArticle')
		           ->will($this->returnCallback(array($this, 'callbackGetArticle')));

		// Register the mock DAO.
		DAORegistry::registerDAO('ArticleDAO', $articleDAO);
	}

	/**
	 * Mock and register an PublishedArticleDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockPublishedArticleDAO() {
		// Mock a PublishedArticleDAO.
		$publishedArticleDAO = $this->getMock('PublishedArticleDAO', array('getPublishedArticleByArticleId'), array(), '', false);

		// Mock a published article.
		$publishedArticle = new PublishedArticle();

		// Mock the getPublishedArticleByArticleId() method.
		$publishedArticleDAO->expects($this->any())
		                    ->method('getPublishedArticleByArticleId')
		                    ->will($this->returnValue($publishedArticle));

		// Register the mock DAO.
		DAORegistry::registerDAO('PublishedArticleDAO', $publishedArticleDAO);
	}

	/**
	 * Mock and register an IssueDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockIssueDAO() {
		// Mock an IssueDAO.
		$issueDAO = $this->getMock('IssueDAO', array('getIssueById'), array(), '', false);

		// Mock an issue.
		$issue = new Issue();

		// Mock the getIssueById() method.
		$issueDAO->expects($this->any())
		         ->method('getIssueById')
		         ->will($this->returnValue($issue));

		// Register the mock DAO.
		DAORegistry::registerDAO('IssueDAO', $issueDAO);
	}

	/**
	 * Mock and register an JournalDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockJournalDAO() {
		// Mock a JournalDAO.
		$journalDAO = $this->getMock('JournalDAO', array('getById'), array(), '', false);

		// Mock a journal.
		$journal = new Journal();

		// Mock the getById() method.
		$journalDAO->expects($this->any())
		           ->method('getById')
		           ->will($this->returnValue($journal));

		// Register the mock DAO.
		DAORegistry::registerDAO('JournalDAO', $journalDAO);
	}

	/**
	 * Mock and register an SectionDAO as a test
	 * back end for the ArticleSearch class.
	 */
	private function registerMockSectionDAO() {
		// Mock a SectionDAO.
		$sectionDAO = $this->getMock('SectionDAO', array('getSection'), array(), '', false);

		// Mock a section.
		$section = new Section();

		// Mock the getSection() method.
		$sectionDAO->expects($this->any())
		           ->method('getSection')
		           ->will($this->returnValue($section));

		// Register the mock DAO.
		DAORegistry::registerDAO('SectionDAO', $sectionDAO);
	}
}
?>