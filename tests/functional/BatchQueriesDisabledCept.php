<?php

$I = new FunctionalTester( $scenario );
$I->wantTo( 'Test batch queries return errors when batching is disabled' );

$options = [
	'enable_batch_queries' => 'off'
];

$I->haveOptionInDatabase( 'graphql_general_settings', $options );

$settings = $I->grabOptionFromDatabase( 'graphql_general_settings' );

$I->haveHttpHeader( 'Content-Type', 'application/json' );

$I->sendPost( 'http://localhost/graphql', json_encode([
	[
		'query' => '{posts{nodes{id,title}}}',
	],
	[
		'query' => '{posts{nodes{id,uri}}}'
	]
]));

$I->seeResponseCodeIs( 200 );
$I->seeResponseIsJson();
$response       = $I->grabResponse();
$response_array = json_decode( $response, true );

$I->assertSame( 'off', $settings['enable_batch_queries'] );

$I->assertArrayHasKey( 'errors', $response_array[0], 'Batch Queries are NOT enabled and the first query should have errors' );
$I->assertArrayNotHasKey( 'data', $response_array[0], 'Batch Queries are NOT enabled and the first query should not have data' );
$I->assertArrayHasKey( 'errors', $response_array[1], 'Batch Queries are NOT enabled and the second query should have errors' );
$I->assertArrayNotHasKey( 'data', $response_array[1], 'Batch Queries are NOT enabled and the second query should not have data' );

