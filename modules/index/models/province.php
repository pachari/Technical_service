<?php
/**
 * @filesource modules/index/models/province.php
 *
 
 */

namespace Index\Province;

use Kotchasan\Http\Request;

/**
 * module=province
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * คืนค่ารายชื่อจังหวัด
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function toJSON(Request $request)
    {
        // referer, ajax
        if ($request->isReferer() && $request->isAjax()) {
            echo json_encode(array(
                'province' => \Kotchasan\Province::all($request->post('country')->filter('A-Z')),
            ));
        }
    }
}
