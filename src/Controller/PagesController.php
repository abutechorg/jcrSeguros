<?php
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 26/8/2016
 * Time: 10:26
 */

namespace App\Controller;

use App\Controller\AppController;

class PagesController extends AppController
{
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count)
        {
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0]))
        {
            $page = $path[0];
        }
        if (!empty($path[1]))
        {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try
        {
            $this->render(implode('/', $path));
        }
        catch (MissingTemplateException $e)
        {
            if (Configure::read('debug'))
            {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
}