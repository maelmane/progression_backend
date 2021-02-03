<?php
/*
  This file is part of Progression.

  Progression is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Progression is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/
?><?php

    namespace progression\http\controleurs;

    use DateTime;
    use \Firebase\JWT\JWT;
    use Exception;

    class Test extends Controller
    {
        function __construct()
        {
        }

        public function test()
        {
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJvYmpldFVzZXIiOnsidXNlcm5hbWUiOiJib2IiLCJyb2xlIjowLCJpZCI6NDIsImFjdGlmIjpudWxsfSwiZGF0ZSI6IjAyLTAyLTIwMjEifQ.IRoU6q-cB6XkXYGM_O6HIa6n8qz0qUpHc5TDsfMBEcE";
            try {
                $tokenDécodé = JWT::decode($token, env('JWT_SECRET'), array('HS256'));
                $différence = (date_diff(new DateTime($tokenDécodé->date), (new DateTime("now"))))->format("%a"); //date_create_from_format("D, d M Y H:i:s e", $token->date));

                if ((int)$différence >= 1) {
                    return "UN";
                } else {
                    return "ZÉRO";
                }
                return response()->json($différence);
            } catch (Exception $e) {
                return "null";
            }
        }
    }

    ?>