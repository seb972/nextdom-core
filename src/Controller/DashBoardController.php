<?php

/* This file is part of NextDom Software.
 *
 * NextDom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NextDom Software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom Software. If not, see <http://www.gnu.org/licenses/>.
 *
 * @Support <https://www.nextdom.org>
 * @Email   <admin@nextdom.org>
 * @Authors/Contributors: Sylvaner, Byackee, cyrilphoenix71, ColonelMoutarde, edgd1er, slobberbone, Astral0, DanoneKiD
 */

namespace NextDom\Controller;

use NextDom\Helpers\NextDomHelper;
use NextDom\Helpers\Render;
use NextDom\Helpers\Utils;
use NextDom\Managers\ConfigManager;
use NextDom\Managers\EqLogicManager;
use NextDom\Managers\JeeObjectManager;
use NextDom\Managers\UserManager;
use NextDom\Model\Entity\JeeObject;

/**
 * Description of toto
 *
 * @author luc
 */
class DashBoardController extends BaseController
{
    /**
     * Render dashboard
     *
     * @param array $pageData Page data
     *
     * @return string Content of Dashboard V2 page
     *
     * @throws \Exception
     */
    public static function get(&$pageData): string
    {
        $objectIdFromUrl = Utils::init('object_id', '');
        $pageData['JS_VARS']['nextdom_Welcome'] = ConfigManager::byKey('nextdom::Welcome');
        $pageData['JS_VARS']['SEL_OBJECT_ID'] = $objectIdFromUrl;
        $pageData['JS_VARS']['SEL_CATEGORY'] = Utils::init('category', 'all');
        $pageData['JS_VARS']['SEL_TAG'] = Utils::init('tag', 'all');
        $pageData['JS_VARS']['SEL_SUMMARY'] = Utils::init('summary');

        if ($pageData['JS_VARS']['SEL_OBJECT_ID'] == '') {
            $defaultDashboardObject = UserManager::getStoredUser()->getOptions('defaultDashboardObject');
            $currentJeeObject = JeeObjectManager::byId($defaultDashboardObject);
            $pageData['JS_VARS']['SEL_OBJECT_ID'] = $defaultDashboardObject;
        } else {
            $currentJeeObject = JeeObjectManager::byId($objectIdFromUrl);
        }

        if (!is_object($currentJeeObject)) {
            $currentJeeObject = JeeObjectManager::getRootObjects();
            if ($currentJeeObject) {
                $pageData['JS_VARS']['SEL_OBJECT_ID'] = $currentJeeObject->getId();
            }
            else {
                throw new \Exception(__('Aucun objet racine trouvé. Pour en créer un, allez dans dashboard -> <a href="/index.php?v=d&p=object">Liste objets et résumés</a>'));
            }
        }

        $pageData['JS_VARS']['rootObjectId'] = $currentJeeObject->getId();

        $pageData['dashboardDisplayObjectByDefault'] = UserManager::getStoredUser()->getOptions('displayObjetByDefault');
        $pageData['dashboardDisplayScenarioByDefault'] = UserManager::getStoredUser()->getOptions('displayScenarioByDefault');
        $pageData['dashboardCategory'] = $pageData['JS_VARS']['SEL_CATEGORY'];
        $pageData['dashboardTag'] = $pageData['JS_VARS']['SEL_TAG'];
        $pageData['dashboardCategories'] = NextDomHelper::getConfiguration('eqLogic:category', true);
        $pageData['dashboardTags'] = EqLogicManager::getAllTags();
        $pageData['dashboardObjectId'] = $pageData['JS_VARS']['SEL_OBJECT_ID'];
        $pageData['dashboardObject'] = $currentJeeObject;
        $pageData['dashboardObjectListMenu'] = self::getObjectsListMenu($pageData['dashboardObjectId']);
        $pageData['dashboardChildrenObjects'] = JeeObjectManager::buildTree($currentJeeObject);
        $pageData['profilsUser'] = UserManager::getStoredUser();

        $pageData['JS_POOL'][] = '/public/js/desktop/dashboard.js';
        $pageData['JS_END_POOL'][] = '/public/js/desktop/dashboard_events.js';
        // A remettre une fois mise sous forme de thème
        $pageData['JS_POOL'][] = '/vendor/node_modules/isotope-layout/dist/isotope.pkgd.min.js';
        $pageData['JS_POOL'][] = '/assets/3rdparty/jquery.multi-column-select/multi-column-select.js';
        $pageData['JS_END_POOL'][] = '/public/js/adminlte/utils.js';

        return Render::getInstance()->get('/desktop/dashboard.html.twig', $pageData);
    }

    /**
     * Get layers
     * @param JeeObject $currentObjectId
     * @return array
     * @throws \Exception
     */
    private static function getObjectsListMenu($currentObjectId) {
        if ($currentObjectId === '') {
            return [];
        }
        $currentObject = JeeObjectManager::byId($currentObjectId);

        // Get parents
        $parentObjects = [];
        $parentObjects[] = $currentObject;
        $father = $currentObject->getFather();
        while ($father !== null) {
            $parentObjects[] = $father;
            $father = $father->getFather();
        }
        $parentObjects = array_reverse($parentObjects);

        // Get all layers before current object
        $result = [];
        foreach ($parentObjects as $selectedLayerObject) {
            $layerResult = [];
            if ($selectedLayerObject->getFather() === null) {
                $layer = JeeObjectManager::getRootObjects(true);
            }
            else {
                $layer = $selectedLayerObject->getFather()->getChild();
            }
            foreach ($layer as $item) {
                $itemData = [];
                $itemData['jeeObject'] = $item;
                $itemData['active'] = false;
                if ($item->getId() == $selectedLayerObject->getId()) {
                    $itemData['active'] = true;
                }
                $layerResult[] = $itemData;
            }
            $result[] = $layerResult;
        }
        // Add children layer
        $children = $currentObject->getChild(true);
        $childrenLayer = [];
        foreach ($children as $item) {
            $itemData = [];
            $itemData['jeeObject'] = $item;
            $itemData['active'] = false;
            $childrenLayer[] = $itemData;
        }
        $result[] = $childrenLayer;
        return $result;
    }
}
