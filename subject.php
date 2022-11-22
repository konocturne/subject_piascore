<?php

	$before_url = 'https://store.piascore.com/search?i=10' ;
	// GETメソッドで指定がある場合は上書き
	if( isset( $_GET['url'] ) && !empty( $_GET['url'] ) ) {
		$before_url = $_GET['url'] ;
	}

	// cURLを利用してリクエスト
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL, 'http://is.gd/create.php?format=simple&format=json&url=' . rawurlencode( $before_url ) ) ;
	curl_setopt( $curl, CURLOPT_HEADER, 1 ) ;						// ヘッダーを取得する
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;			// 証明書の検証を行わない
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;			// curl_execの結果を文字列で返す
	curl_setopt( $curl, CURLOPT_TIMEOUT, 15 ) ;						// タイムアウトの秒数
	curl_setopt( $curl, CURLOPT_FOLLOWLOCATION , true ) ;			// リダイレクト先を追跡するか？
	curl_setopt( $curl, CURLOPT_MAXREDIRS, 5 ) ;					// 追跡する回数
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// 取得したデータ
	$json = substr( $res1, $res2['header_size'] ) ;		// 取得したデータ(JSONなど)
	$header = substr( $res1, 0, $res2['header_size'] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

	// 取得したJSONをオブジェクトに変換
	$obj = json_decode( $json ) ;

	// URLを表示用に整形 (検証用)
	foreach( array( 'before_url', ) as $variable_name ) {
		${ $variable_name } = htmlspecialchars( ${ $variable_name } , ENT_QUOTES , 'UTF-8' ) ;
	}

	// 出力
	$html .= '<h2 style = "text-align:center">URL短縮サイト</h2>' ;
	$html .= '<body style = "background: linear-gradient(135deg, #FFFFFF, #14EFFF);">';
	// 成功時
	if( isset( $obj->shorturl ) && !empty( $obj->shorturl ) ) {
		// 取得した短縮URL
		$shorten_url = $obj->shorturl ;
		// 出力
		$html .= '<dl style = "text-align:center">' ;
		$html .= 	'<dt style = "margin-top : 100px; margin-top : 100px;">オリジナルURL</dt>' ;
		$html .= 		'<dd style = "margin : 0"> <a href="' . $before_url . '" target="_blank">' . $before_url . '</a></dd>' ;
		$html .= 	'<dt style = "margin-top : 70px">短縮したURL</dt>' ;
		$html .= 		'<dd style = "margin : 0"> <a href="' . $shorten_url . '" target="_blank">' . $shorten_url . '</a></dd>' ;
		$html .= '</dl>' ;
	// 失敗時
	} else {
		$html .= '<h2 style = "text-align : center; margin :100px 0px 10px; font-size : 80px;"><mark style = "background : red">Error</mark></h2>';
		$html .= '<p style = "text-align : center">このURLは対応していません</p>';
	}


	// URLの入力
	$html .= '<div style = "text-align:center">';
	$html .= '<h2 style = "margin-top: 100px">短縮したいURLを入力してください</h2>' ;
	$html .= '<form method = "post">' ;
	$html .= 	'<p><input name="url" size = "100" placeholder="https://store.piascore.com/search?i=10" value="' . $before_url . '"></p>' ;
	$html .= 	'<p><button style = "width: 100px; height:50px; font-size: 20px; ">実行</button></p>' ;
	$html .= '</form>' ;
	$html .= '</div>';
	// ブラウザに[$html]の内容を出力
	echo $html ;