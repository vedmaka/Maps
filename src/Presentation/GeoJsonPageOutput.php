<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Html;

class GeoJsonPageOutput {

	private $json;

	public static function forNewPage(): self {
		return new self( null );
	}

	public static function forExistingPage( string $mapJson ): self {
		return new self( $mapJson );
	}

	private function __construct( ?string $json ) {
		$this->json = $json;
	}

	public function addToParserOutput( \ParserOutput $parserOutput ) {
		$parserOutput->setText(  $this->getHtml() );
		$parserOutput->addModules( $this->getResourceModules() );
	}

	public function addToOutputPage( \OutputPage $output ) {
		$output->addHTML(  $this->getHtml() );
		$output->addModules( $this->getResourceModules() );
	}

	private function getResourceModules(): array {
		return [
			'ext.maps.leaflet.loader'
		];
	}

	private function getHtml(): string {
		return '<div id="GeoJsonMap" class="maps-leaflet" style="height: 400px; width: 800px"></div>';
	}

}
