<?php

namespace Maps\MediaWiki\ParserHooks;

use Maps\MapsFactory;
use ParamProcessor\ParamDefinition;
use ParserHook;

/**
 * Class for the 'mapsdoc' parser hooks,
 * which displays documentation for a specified mapping service.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDocFunction extends ParserHook {

	/**
	 * Field to store the value of the language parameter.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * Renders and returns the output.
	 *
	 * @see ParserHook::render
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function render( array $parameters ) {
		$this->language = $parameters['language'];

		$factory = MapsFactory::globalInstance();

		$params = $this->getServiceParameters( $factory, $parameters['service'] );

		return $this->getParameterTable( $factory, $params );
	}

	private function getServiceParameters( MapsFactory $factory, string $service ) {
		return array_merge(
			[
				'zoom' => [
					'type' => 'integer',
					'message' => 'maps-par-zoom',
				]
			],
			$factory->getMappingServices()->getService( $service )->getParameterInfo()
		);
	}

	/**
	 * Returns the wikitext for a table listing the provided parameters.
	 */
	private function getParameterTable( MapsFactory $factory, array $parameters ): string {
		$tableRows = [];

		$parameters = $factory->getParamDefinitionFactory()->newDefinitionsFromArrays( $parameters );

		foreach ( $parameters as $parameter ) {
			$tableRows[] = $this->getDescriptionRow( $parameter );
		}

		$table = '';

		if ( count( $tableRows ) > 0 ) {
			$tableRows = array_merge(
				[
					'!' . $this->msg( 'validator-describe-header-parameter' ) . "\n" .
					//'!' . $this->msg( 'validator-describe-header-aliases' ) ."\n" .
					'!' . $this->msg( 'validator-describe-header-type' ) . "\n" .
					'!' . $this->msg( 'validator-describe-header-default' ) . "\n" .
					'!' . $this->msg( 'validator-describe-header-description' )
				],
				$tableRows
			);

			$table = implode( "\n|-\n", $tableRows );

			$table =
				'{| class="wikitable sortable"' . "\n" .
				$table .
				"\n|}";
		}

		return $table;
	}

	/**
	 * Returns the wikitext for a table row describing a single parameter.
	 *
	 * @param ParamDefinition $parameter
	 *
	 * @return string
	 */
	private function getDescriptionRow( ParamDefinition $parameter ) {
		$description = $this->msg( $parameter->getMessage() );

		$type = $this->msg( $parameter->getTypeMessage() );

		$default = $parameter->isRequired() ? "''" . $this->msg(
				'validator-describe-required'
			) . "''" : $parameter->getDefault();
		if ( is_array( $default ) ) {
			$default = implode( ', ', $default );
		} elseif ( is_bool( $default ) ) {
			$default = $default ? 'yes' : 'no';
		}

		if ( $default === '' ) {
			$default = "''" . $this->msg( 'validator-describe-empty' ) . "''";
		}

		return <<<EOT
| {$parameter->getName()}
| {$type}
| {$default}
| {$description}
EOT;
	}

	/**
	 * Message function that takes into account the language parameter.
	 *
	 * @param string $key
	 * @param ... $args
	 *
	 * @return string
	 */
	private function msg() {
		$args = func_get_args();
		$key = array_shift( $args );
		return wfMessage( $key, $args )->inLanguage( $this->language )->text();
	}

	/**
	 * @see ParserHook::getDescription()
	 */
	public function getMessage() {
		return 'maps-mapsdoc-description';
	}

	/**
	 * Gets the name of the parser hook.
	 *
	 * @see ParserHook::getName
	 *
	 * @return string
	 */
	protected function getName() {
		return 'mapsdoc';
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @see ParserHook::getParameterInfo
	 *
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		$params = [];

		$params['service'] = [
			'values' => $GLOBALS['egMapsAvailableServices'],
			'tolower' => true,
		];

		$params['language'] = [
			'default' => $GLOBALS['wgLanguageCode'],
		];

		// Give grep a chance to find the usages:
		// maps-geocode-par-service, maps-geocode-par-language
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-geocode-par-' . $name;
		}

		return $params;
	}

	/**
	 * Returns the list of default parameters.
	 *
	 * @see ParserHook::getDefaultParameters
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return [ 'service', 'language' ];
	}

}
