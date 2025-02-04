<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Salutation;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\System;

class SalutationUtil
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Creates complete names by inserting an array of the person's data.
     *
     * Supported field names: firstname, lastname, academicTitle, additionalTitle, gender
     *
     * If some of the fields shouldn't go into the processed name, just leave them out of $arrData
     */
    public function createNameByFields(string $language, array $data)
    {
        if ($language) {
            /** @var System $system */
            $system = $this->framework->getAdapter(System::class);

            $system->loadLanguageFile('default', $language, true);
        }

        $name = '';

        if ($data['firstname']) {
            $name = $data['firstname'].($data['lastname'] ? ' '.$data['lastname'] : '');
        } elseif ($data['lastname']) {
            $name = $data['lastname'];
        }

        if ($name && $data['academicTitle']) {
            $name = $data['academicTitle'].' '.$name;
        }

        if ($name && $data['additionalTitle']) {
            $name = $data['additionalTitle'].' '.$name;
        }

        if ($data['lastname'] && $data['gender'] && !\in_array($data['gender'], ['other', 'divers']) && ('en' != $language || !$data['academicTitle'])) {
            $gender = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['gender'.('female' == $data['gender'] ? 'Female' : 'Male')];

            $name = $gender.' '.$name;
        }

        if ($language) {
            $system->loadLanguageFile('default', $GLOBALS['TL_LANGUAGE'], true);
        }

        return $name;
    }

    /**
     * @param $language
     * @param $entity object|array
     *
     * @return string
     */
    public function createSalutation(string $language, $entity, array $options = [])
    {
        $informal = isset($options['informal']) && $options['informal'];
        $firstnameOnly = isset($options['firstnameOnly']) && $options['firstnameOnly'];

        if (\is_array($entity)) {
            $entity = System::getContainer()->get('huh.utils.array')->arrayToObject($entity);
        }

        $hasFirstname = $entity->firstname;
        $hasLastname = $entity->lastname;
        $hasTitle = $entity->title && '-' != $entity->title && 'Titel' != $entity->title && 'Title' != $entity->title;

        if (!$hasTitle) {
            $hasTitle = $entity->academicTitle && '-' != $entity->academicTitle && 'Titel' != $entity->academicTitle && 'Title' != $entity->academicTitle;
        }

        if ($language) {
            /** @var System $system */
            $system = $this->framework->getAdapter(System::class);

            $system->loadLanguageFile('default', $language, true);
        }

        switch ($language) {
            case 'en':
                if (\in_array($entity->gender, ['other', 'divers'])) {
                    if ($informal) {
                        if ($hasFirstname && $firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationDivers'].' '.$entity->firstname;
                        } elseif ($hasFirstname && $hasLastname && !$firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationDivers'].' '.$entity->firstname.' '.$entity->lastname;
                        } else {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationDivers'];
                        }
                    } else {
                        $suffix = $entity->firstname && $entity->lastname ? ' '.$entity->firstname.' '.$entity->lastname : '';
                        $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationDivers'].$suffix;
                    }
                } else {
                    if ($informal) {
                        if ($hasFirstname && $firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'].' '.$entity->firstname;
                        } elseif ($hasLastname) {
                            $salutationPart = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['gender'.('female' == $entity->gender ? 'Female' : 'Male')];

                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'].' '.$salutationPart.' '.$entity->lastname;
                        } else {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'];
                        }
                    } elseif ($hasLastname) {
                        if ($hasTitle) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'].' '.($entity->title ?: $entity->academicTitle);
                        } else {
                            $salutation =
                                $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'.('female' == $entity->gender ? 'Female' : 'Male')];
                        }

                        $salutation = $salutation.' '.$entity->lastname;
                    } else {
                        $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'];
                    }
                }

                break;

            default:
                // de
                if (\in_array($entity->gender, ['other', 'divers'])) {
                    if ($informal) {
                        if ($hasFirstname && $firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'].' '.$entity->firstname;
                        } elseif ($hasFirstname && $hasLastname && !$firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'].' '.$entity->firstname.' '.$entity->lastname;
                        } else {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'];
                        }
                    } else {
                        $suffix = $entity->firstname && $entity->lastname ? ', '.$entity->firstname.' '.$entity->lastname : '';
                        $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationDivers'].$suffix;
                    }
                } else {
                    if ($informal) {
                        if ($hasFirstname && $firstnameOnly) {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'].' '.$entity->firstname;
                        } elseif ($hasLastname && !$firstnameOnly) {
                            $salutationPart = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['gender'.('female' == $entity->gender ? 'Female' : 'Male')];
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'].' '.$salutationPart.' '.$entity->lastname;
                        } else {
                            $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGenericInformal'];
                        }
                    } elseif ($hasLastname && !$informal) {
                        $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutation'.('female' == $entity->gender ? 'Female' : 'Male')];

                        if ($hasTitle) {
                            $salutation .= ' '.($entity->title ?: $entity->academicTitle);
                        }

                        $salutation = $salutation.' '.$entity->lastname;
                    } else {
                        $salutation = $GLOBALS['TL_LANG']['MSC']['utilsBundle']['salutationGeneric'];
                    }
                }

                break;
        }

        if ($language) {
            $system->loadLanguageFile('default', $GLOBALS['TL_LANGUAGE'], true);
        }

        return $salutation;
    }
}
