<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Admin;
use App\Entity\Ajouter;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Contact;
use App\Entity\Noter;
use App\Entity\Panier;
use App\Entity\Parvenir;
use App\Entity\Produit;
use App\Entity\ProduitImage;
use App\Entity\Sav;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\AdminType;
use App\Form\AjouterType;
use App\Form\CategorieType;
use App\Form\CommandeType;
use App\Form\ContactType;
use App\Form\NoterType;
use App\Form\PanierType;
use App\Form\ParvenirType;
use App\Form\ProduitType;
use App\Form\SavType;
use App\Form\UserAdminType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    private const PRODUCT_IMAGE_MAX_SIZE = 10485760;
    private const PRODUCT_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * @var string[]
     */
    private array $productImageFilesPendingDeletion = [];

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $sections = $this->adminSections($entityManager);

        return $this->render('admin/dashboard.html.twig', [
            'metrics' => [
                [
                    'label' => 'Produits',
                    'value' => $entityManager->getRepository(Produit::class)->count([]),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Commandes',
                    'value' => $entityManager->getRepository(Commande::class)->count([]),
                    'tone' => 'secondary',
                ],
                [
                    'label' => 'Utilisateurs',
                    'value' => $entityManager->getRepository(User::class)->count([]),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'SAV',
                    'value' => $entityManager->getRepository(Sav::class)->count([]),
                    'tone' => 'danger',
                ],
                [
                    'label' => 'Contacts',
                    'value' => $entityManager->getRepository(Contact::class)->count([]),
                    'tone' => 'secondary',
                ],
            ],
            'admin_sections' => $sections,
        ]);
    }

    #[Route('/inventaire', name: 'app_admin_inventory')]
    public function inventory(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('produits', $entityManager);
    }

    #[Route('/categories', name: 'app_admin_categories')]
    public function categories(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('categories', $entityManager);
    }

    #[Route('/commandes', name: 'app_admin_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('commandes', $entityManager);
    }

    #[Route('/utilisateurs', name: 'app_admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('utilisateurs', $entityManager);
    }

    #[Route('/support', name: 'app_admin_support')]
    public function support(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('sav', $entityManager);
    }

    #[Route('/avis', name: 'app_admin_reviews')]
    public function reviews(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('avis', $entityManager);
    }

    #[Route('/contacts', name: 'app_admin_contacts')]
    public function contacts(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('contacts', $entityManager);
    }

    #[Route('/droits', name: 'app_admin_permissions')]
    public function permissions(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('admins', $entityManager);
    }

    #[Route('/adresses', name: 'app_admin_addresses')]
    public function addresses(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('adresses', $entityManager);
    }

    #[Route('/paniers', name: 'app_admin_carts')]
    public function carts(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('paniers', $entityManager);
    }

    #[Route('/lignes-panier', name: 'app_admin_cart_lines')]
    public function cartLines(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('ajouter', $entityManager);
    }

    #[Route('/lignes-commandes', name: 'app_admin_order_lines')]
    public function orderLines(EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex('parvenir', $entityManager);
    }

    #[Route('/gestion/{resource}', name: 'app_admin_crud_index', methods: ['GET'])]
    public function index(string $resource, EntityManagerInterface $entityManager): Response
    {
        return $this->renderIndex($resource, $entityManager);
    }

    #[Route('/gestion/{resource}/nouveau', name: 'app_admin_crud_new', methods: ['GET', 'POST'])]
    public function new(string $resource, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $config = $this->resourceConfig($resource);
        $entity = $this->newEntity($config['entity']);
        $form = $this->createResourceForm($resource, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->prepareEntity($entity, $form, true, $slugger)) {
            try {
                $entityManager->persist($entity);
                $entityManager->flush();
                $this->deletePendingProductImageFiles($entityManager);
                $this->addFlash('success', 'Enregistrement ajouté.');

                return $this->redirectToRoute('app_admin_crud_index', ['resource' => $resource]);
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Cette donnée existe déjà avec les mêmes contraintes.');
            }
        }

        return $this->renderFormPage($resource, $config['new_title'], $form, $entity);
    }

    #[Route('/gestion/{resource}/{id}/modifier', name: 'app_admin_crud_edit', methods: ['GET', 'POST'])]
    public function edit(string $resource, int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $config = $this->resourceConfig($resource);
        $entity = $entityManager->getRepository($config['entity'])->find($id);

        if ($entity === null) {
            throw $this->createNotFoundException('Enregistrement introuvable.');
        }

        $form = $this->createResourceForm($resource, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->prepareEntity($entity, $form, false, $slugger)) {
            try {
                $entityManager->flush();
                $this->deletePendingProductImageFiles($entityManager);
                $this->addFlash('success', 'Enregistrement modifié.');

                return $this->redirectToRoute('app_admin_crud_index', ['resource' => $resource]);
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Cette donnée existe déjà avec les mêmes contraintes.');
            }
        }

        return $this->renderFormPage($resource, $config['edit_title'], $form, $entity);
    }

    #[Route('/gestion/{resource}/{id}/supprimer', name: 'app_admin_crud_delete', methods: ['POST'])]
    public function delete(string $resource, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $config = $this->resourceConfig($resource);
        $entity = $entityManager->getRepository($config['entity'])->find($id);

        if ($entity !== null && $this->isCsrfTokenValid('delete-' . $resource . '-' . $id, (string) $request->request->get('_token'))) {
            $productImageFilenames = $entity instanceof Produit ? $this->collectProductImageFilenames($entity) : [];

            try {
                $entityManager->remove($entity);
                $entityManager->flush();

                foreach ($productImageFilenames as $filename) {
                    $this->deleteProductImageFileIfUnused($filename, $entityManager);
                }

                $this->addFlash('success', 'Enregistrement supprimé.');
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Suppression impossible : cette donnée est encore utilisée ailleurs.');
            }
        }

        return $this->redirectToRoute('app_admin_crud_index', ['resource' => $resource]);
    }

    #[Route('/gestion/produits/{productId<\d+>}/images/{imageId<\d+>}/principale', name: 'app_admin_product_image_primary', methods: ['POST'])]
    public function makeProductImagePrimary(int $productId, int $imageId, Request $request, EntityManagerInterface $entityManager): Response
    {
        [$produit, $image] = $this->findProductAndImage($productId, $imageId, $entityManager);

        if (!$this->isCsrfTokenValid('primary-product-image-' . $imageId, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $produit->setImage((string) $image->getFilename());
        $this->reindexProductImages($produit, $image);
        $entityManager->flush();

        $this->addFlash('success', 'Image principale mise à jour.');

        return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
    }

    #[Route('/gestion/produits/{productId<\d+>}/images/{imageId<\d+>}/remplacer', name: 'app_admin_product_image_replace', methods: ['POST'])]
    public function replaceProductImage(int $productId, int $imageId, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        [$produit, $image] = $this->findProductAndImage($productId, $imageId, $entityManager);

        if (!$this->isCsrfTokenValid('replace-product-image-' . $imageId, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $imageFile = $request->files->get('imageFile');
        if (!$imageFile instanceof UploadedFile) {
            $this->addFlash('danger', 'Veuillez sélectionner une image à remplacer.');

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $validationError = $this->validateUploadedProductImage($imageFile);
        if ($validationError !== null) {
            $this->addFlash('danger', $validationError);

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $previousFilename = $image->getFilename();
        $serverFilename = $this->storeUploadedProductImage($imageFile, $slugger);

        if ($serverFilename === null) {
            $this->addFlash('danger', 'Erreur pendant l’envoi de l’image.');

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $image
            ->setFilename($serverFilename)
            ->setAlt($produit->getDesignation());

        if ($previousFilename === $produit->getImage()) {
            $produit->setImage($serverFilename);
        }

        $entityManager->flush();
        $this->deleteProductImageFileIfUnused($previousFilename, $entityManager);

        $this->addFlash('success', 'Image remplacée.');

        return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
    }

    #[Route('/gestion/produits/{productId<\d+>}/images/{imageId<\d+>}/supprimer', name: 'app_admin_product_image_delete', methods: ['POST'])]
    public function deleteProductImage(int $productId, int $imageId, Request $request, EntityManagerInterface $entityManager): Response
    {
        [$produit, $image] = $this->findProductAndImage($productId, $imageId, $entityManager);

        if (!$this->isCsrfTokenValid('delete-product-image-' . $imageId, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');

            return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
        }

        $deletedFilename = $image->getFilename();
        $deletingPrimary = $deletedFilename === $produit->getImage();
        $produit->removeImage($image);

        if ($deletingPrimary) {
            $fallbackImage = $this->firstProductImage($produit);

            if (!$fallbackImage instanceof ProduitImage || $fallbackImage->getFilename() === null) {
                $produit->addImage($image);
                $this->addFlash('danger', 'Un produit doit conserver au moins une image principale.');

                return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
            }

            $produit->setImage($fallbackImage->getFilename());
            $this->reindexProductImages($produit, $fallbackImage);
        } else {
            $this->reindexProductImages($produit, $this->findProductImageByFilename($produit, $produit->getImage()));
        }

        $entityManager->remove($image);
        $entityManager->flush();
        $this->deleteProductImageFileIfUnused($deletedFilename, $entityManager);

        $this->addFlash('success', 'Image supprimée.');

        return $this->redirectToRoute('app_admin_crud_edit', ['resource' => 'produits', 'id' => $productId]);
    }

    private function renderIndex(string $resource, EntityManagerInterface $entityManager): Response
    {
        $config = $this->resourceConfig($resource);
        $items = $entityManager->getRepository($config['entity'])->findBy([], $config['order']);

        return $this->render('admin/resource_table.html.twig', [
            'admin_active' => $config['active'],
            'sidebar_id' => $config['active'] . '-sidebar',
            'overlay_id' => $config['active'] . '-overlay',
            'resource' => $resource,
            'title' => $config['title'],
            'kicker' => $config['kicker'],
            'columns' => $config['columns'],
            'rows' => $this->rows($resource, $items),
            'empty_message' => $config['empty'],
        ]);
    }

    private function renderFormPage(string $resource, string $title, FormInterface $form, ?object $entity = null): Response
    {
        $config = $this->resourceConfig($resource);

        return $this->render('admin/crud/form.html.twig', [
            'admin_active' => $config['active'],
            'sidebar_id' => $config['active'] . '-form-sidebar',
            'overlay_id' => $config['active'] . '-form-overlay',
            'title' => $title,
            'kicker' => $config['kicker'],
            'form' => $form->createView(),
            'resource' => $resource,
            'entity' => $entity,
            'back_path' => $this->generateUrl('app_admin_crud_index', ['resource' => $resource]),
            'submit_label' => 'Envoyer',
        ]);
    }

    private function createResourceForm(string $resource, object $entity): FormInterface
    {
        $config = $this->resourceConfig($resource);
        $options = match ($resource) {
            'produits' => ['image_required' => $entity instanceof Produit && $entity->getImage() === null],
            'adresses' => ['include_user' => true],
            'commandes' => ['admin' => true],
            'avis' => ['admin' => true],
            'sav' => ['admin' => true],
            'utilisateurs' => ['require_password' => $entity instanceof User && $entity->getId() === null],
            'admins' => ['require_password' => $entity instanceof Admin && $entity->getId() === null],
            default => [],
        };

        return $this->createForm($config['form'], $entity, $options);
    }

    private function prepareEntity(object $entity, FormInterface $form, bool $isNew, SluggerInterface $slugger): bool
    {
        $this->productImageFilesPendingDeletion = [];

        if ($entity instanceof Produit && !$this->storeProductImages($entity, $form, $slugger)) {
            return false;
        }

        if ($entity instanceof User) {
            $plainPassword = (string) $form->get('plainPassword')->getData();

            if ($isNew && $plainPassword === '') {
                $form->get('plainPassword')->addError(new FormError('Veuillez saisir un mot de passe.'));

                return false;
            }

            if ($plainPassword !== '' && strlen($plainPassword) < 6) {
                $form->get('plainPassword')->addError(new FormError('Le mot de passe doit contenir au moins 6 caractères.'));

                return false;
            }

            if ($plainPassword !== '') {
                $entity->setPassword($this->passwordHasher->hashPassword($entity, $plainPassword));
            }

            if ($isNew) {
                $entity->setIsVerified(true);
            }
        }

        if ($entity instanceof Admin) {
            $plainPassword = (string) $form->get('plainPassword')->getData();

            if ($isNew && $plainPassword === '') {
                $form->get('plainPassword')->addError(new FormError('Veuillez saisir un mot de passe admin.'));

                return false;
            }

            if ($plainPassword !== '' && strlen($plainPassword) < 6) {
                $form->get('plainPassword')->addError(new FormError('Le mot de passe admin doit contenir au moins 6 caractères.'));

                return false;
            }

            if ($plainPassword !== '') {
                $entity->setMdpA(password_hash($plainPassword, PASSWORD_DEFAULT));
            }

            if ($entity->getUser() !== null) {
                $roles = $entity->getUser()->getRoles();
                $roles[] = 'ROLE_ADMIN';
                $entity->getUser()->setRoles(array_unique($roles));
            }
        }

        if ($entity instanceof Noter && $entity->getDateMessage() === null) {
            $entity->setDateMessage(new \DateTime());
        }

        if ($entity instanceof Sav && $entity->getDateMessage() === null) {
            $entity->setDateMessage(new \DateTime());
        }

        if ($entity instanceof Contact && $entity->getDateEnvoi() === null) {
            $entity->setDateEnvoi(new \DateTime());
        }

        return true;
    }

    private function storeProductImages(Produit $produit, FormInterface $form, SluggerInterface $slugger): bool
    {
        if (!$form->has('imageFile')) {
            return true;
        }

        $imageFile = $form->get('imageFile')->getData();
        $nextPosition = $this->nextProductImagePosition($produit);

        if (!$imageFile instanceof UploadedFile) {
            if ($produit->getImage() === null) {
                $form->get('imageFile')->addError(new FormError('Veuillez sélectionner une image.'));

                return false;
            }
        } else {
            $serverFilename = $this->storeUploadedProductImage($imageFile, $slugger);

            if ($serverFilename === null) {
                $form->get('imageFile')->addError(new FormError('Erreur pendant l’envoi de l’image.'));

                return false;
            }

            $previousFilename = $produit->getImage();
            $primaryImage = $this->findProductImageByFilename($produit, $previousFilename);
            $produit->setImage($serverFilename);

            if ($primaryImage instanceof ProduitImage) {
                $primaryImage
                    ->setFilename($serverFilename)
                    ->setAlt($produit->getDesignation())
                    ->setPosition(0);
            } else {
                $produit->addImage(
                    (new ProduitImage())
                        ->setFilename($serverFilename)
                        ->setAlt($produit->getDesignation())
                        ->setPosition(0)
                );
            }

            if ($previousFilename !== null && $previousFilename !== $serverFilename) {
                $this->productImageFilesPendingDeletion[] = $previousFilename;
            }
        }

        if ($produit->getImages()->isEmpty() && $produit->getImage() !== null) {
            $produit->addImage(
                (new ProduitImage())
                    ->setFilename($produit->getImage())
                    ->setAlt($produit->getDesignation())
                    ->setPosition($nextPosition++)
            );
        }

        if ($form->has('galleryFiles')) {
            $galleryFiles = $form->get('galleryFiles')->getData();

            if (is_iterable($galleryFiles)) {
                foreach ($galleryFiles as $galleryFile) {
                    if (!$galleryFile instanceof UploadedFile) {
                        continue;
                    }

                    $serverFilename = $this->storeUploadedProductImage($galleryFile, $slugger);

                    if ($serverFilename === null) {
                        $form->get('galleryFiles')->addError(new FormError('Erreur pendant l’envoi d’une photo de galerie.'));

                        return false;
                    }

                    $produit->addImage(
                        (new ProduitImage())
                            ->setFilename($serverFilename)
                            ->setAlt($produit->getDesignation())
                            ->setPosition($nextPosition++)
                    );
                }
            }
        }

        $this->reindexProductImages($produit, $this->findProductImageByFilename($produit, $produit->getImage()));

        return true;
    }

    private function storeUploadedProductImage(UploadedFile $imageFile, SluggerInterface $slugger): ?string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $slugger->slug($originalFilename);
        $extension = $imageFile->guessExtension() ?: $imageFile->getClientOriginalExtension();
        $serverFilename = $safeFilename . '-' . uniqid('', true) . '.' . strtolower($extension);

        try {
            $imageFile->move($this->getParameter('product_images_directory'), $serverFilename);
        } catch (FileException) {
            return null;
        }

        return $serverFilename;
    }

    /**
     * @return array{Produit, ProduitImage}
     */
    private function findProductAndImage(int $productId, int $imageId, EntityManagerInterface $entityManager): array
    {
        $produit = $entityManager->getRepository(Produit::class)->find($productId);
        $image = $entityManager->getRepository(ProduitImage::class)->find($imageId);

        if (!$produit instanceof Produit || !$image instanceof ProduitImage || $image->getProduit()?->getId() !== $produit->getId()) {
            throw $this->createNotFoundException('Image produit introuvable.');
        }

        return [$produit, $image];
    }

    private function validateUploadedProductImage(UploadedFile $imageFile): ?string
    {
        if ($imageFile->getSize() !== null && $imageFile->getSize() > self::PRODUCT_IMAGE_MAX_SIZE) {
            return 'Chaque image doit faire 10 Mo maximum.';
        }

        if (!in_array((string) $imageFile->getMimeType(), self::PRODUCT_IMAGE_MIME_TYPES, true)) {
            return 'Le site accepte uniquement les images JPG, PNG, WEBP et GIF.';
        }

        return null;
    }

    private function nextProductImagePosition(Produit $produit): int
    {
        $position = -1;

        foreach ($produit->getImages() as $image) {
            $position = max($position, $image->getPosition());
        }

        return $position + 1;
    }

    private function findProductImageByFilename(Produit $produit, ?string $filename): ?ProduitImage
    {
        if ($filename === null) {
            return null;
        }

        foreach ($produit->getImages() as $image) {
            if ($image->getFilename() === $filename) {
                return $image;
            }
        }

        return null;
    }

    private function firstProductImage(Produit $produit): ?ProduitImage
    {
        foreach ($produit->getImages() as $image) {
            if ($image->getFilename() !== null) {
                return $image;
            }
        }

        return null;
    }

    private function reindexProductImages(Produit $produit, ?ProduitImage $primaryImage = null): void
    {
        $position = 0;

        if ($primaryImage instanceof ProduitImage && $produit->getImages()->contains($primaryImage)) {
            $primaryImage->setPosition($position++);
        }

        foreach ($produit->getImages() as $image) {
            if ($image === $primaryImage) {
                continue;
            }

            $image->setPosition($position++);
        }
    }

    /**
     * @return string[]
     */
    private function collectProductImageFilenames(Produit $produit): array
    {
        $filenames = [];

        if ($produit->getImage() !== null) {
            $filenames[] = $produit->getImage();
        }

        foreach ($produit->getImages() as $image) {
            if ($image->getFilename() !== null) {
                $filenames[] = $image->getFilename();
            }
        }

        return array_values(array_unique($filenames));
    }

    private function deletePendingProductImageFiles(EntityManagerInterface $entityManager): void
    {
        foreach (array_unique($this->productImageFilesPendingDeletion) as $filename) {
            $this->deleteProductImageFileIfUnused($filename, $entityManager);
        }

        $this->productImageFilesPendingDeletion = [];
    }

    private function deleteProductImageFileIfUnused(?string $filename, EntityManagerInterface $entityManager): void
    {
        if ($filename === null || !$this->isLocalProductImageFilename($filename)) {
            return;
        }

        if ($this->productImageFilenameIsUsed($filename, $entityManager)) {
            return;
        }

        $path = rtrim((string) $this->getParameter('product_images_directory'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (is_file($path) && !@unlink($path)) {
            $this->addFlash('danger', 'L’image a été supprimée en base, mais le fichier serveur n’a pas pu être supprimé.');
        }
    }

    private function isLocalProductImageFilename(string $filename): bool
    {
        return $filename !== ''
            && !str_contains($filename, "\0")
            && !str_contains($filename, '/')
            && !str_contains($filename, '\\')
            && parse_url($filename, PHP_URL_SCHEME) === null;
    }

    private function productImageFilenameIsUsed(string $filename, EntityManagerInterface $entityManager): bool
    {
        return $entityManager->getRepository(Produit::class)->count(['image' => $filename]) > 0
            || $entityManager->getRepository(ProduitImage::class)->count(['filename' => $filename]) > 0;
    }

    /**
     * @return array<string, mixed>
     */
    private function resourceConfig(string $resource): array
    {
        $configs = [
            'produits' => [
                'active' => 'inventory',
                'title' => 'Produits',
                'kicker' => 'Table Produit',
                'entity' => Produit::class,
                'form' => ProduitType::class,
                'order' => ['designation' => 'ASC'],
                'columns' => ['ID', 'Désignation', 'Catégorie', 'Prix HT', 'Stock'],
                'empty' => 'Aucun produit enregistré.',
                'new_title' => 'Nouveau_Produit',
                'edit_title' => 'Modifier_Produit',
            ],
            'categories' => [
                'active' => 'categories',
                'title' => 'Catégories',
                'kicker' => 'Table Categorie',
                'entity' => Categorie::class,
                'form' => CategorieType::class,
                'order' => ['nomCategorie' => 'ASC'],
                'columns' => ['ID', 'Nom', 'Produits'],
                'empty' => 'Aucune catégorie enregistrée.',
                'new_title' => 'Nouvelle_Catégorie',
                'edit_title' => 'Modifier_Catégorie',
            ],
            'utilisateurs' => [
                'active' => 'users',
                'title' => 'Utilisateurs',
                'kicker' => 'Table User // Utilisateur',
                'entity' => User::class,
                'form' => UserAdminType::class,
                'order' => ['email' => 'ASC'],
                'columns' => ['ID', 'Nom', 'Email', 'Rôles', 'Adresses', 'Commandes'],
                'empty' => 'Aucun utilisateur enregistré.',
                'new_title' => 'Nouvel_Utilisateur',
                'edit_title' => 'Modifier_Utilisateur',
            ],
            'adresses' => [
                'active' => 'addresses',
                'title' => 'Adresses',
                'kicker' => 'Table Adresse',
                'entity' => Adresse::class,
                'form' => AdresseType::class,
                'order' => ['ville' => 'ASC'],
                'columns' => ['ID', 'Client', 'Rue', 'Ville', 'Pays'],
                'empty' => 'Aucune adresse enregistrée.',
                'new_title' => 'Nouvelle_Adresse',
                'edit_title' => 'Modifier_Adresse',
            ],
            'paniers' => [
                'active' => 'carts',
                'title' => 'Paniers',
                'kicker' => 'Table Panier',
                'entity' => Panier::class,
                'form' => PanierType::class,
                'order' => ['id' => 'DESC'],
                'columns' => ['ID', 'Client', 'Total HT', 'Lignes'],
                'empty' => 'Aucun panier enregistré.',
                'new_title' => 'Nouveau_Panier',
                'edit_title' => 'Modifier_Panier',
            ],
            'ajouter' => [
                'active' => 'cart-lines',
                'title' => 'Lignes_Panier',
                'kicker' => 'Table Ajouter',
                'entity' => Ajouter::class,
                'form' => AjouterType::class,
                'order' => ['id' => 'DESC'],
                'columns' => ['ID', 'Panier', 'Produit', 'Quantité', 'Prix HT'],
                'empty' => 'Aucune ligne panier enregistrée.',
                'new_title' => 'Nouvelle_Ligne_Panier',
                'edit_title' => 'Modifier_Ligne_Panier',
            ],
            'commandes' => [
                'active' => 'orders',
                'title' => 'Commandes',
                'kicker' => 'Table Commande',
                'entity' => Commande::class,
                'form' => CommandeType::class,
                'order' => ['dateCommande' => 'DESC'],
                'columns' => ['ID', 'Date', 'Client', 'Adresse', 'Total HT', 'TVA', 'Total TTC'],
                'empty' => 'Aucune commande enregistrée.',
                'new_title' => 'Nouvelle_Commande',
                'edit_title' => 'Modifier_Commande',
            ],
            'parvenir' => [
                'active' => 'order-lines',
                'title' => 'Lignes_Commande',
                'kicker' => 'Table Parvenir',
                'entity' => Parvenir::class,
                'form' => ParvenirType::class,
                'order' => ['id' => 'DESC'],
                'columns' => ['ID', 'Commande', 'Produit', 'Quantité', 'Prix HT'],
                'empty' => 'Aucune ligne commande enregistrée.',
                'new_title' => 'Nouvelle_Ligne_Commande',
                'edit_title' => 'Modifier_Ligne_Commande',
            ],
            'avis' => [
                'active' => 'reviews',
                'title' => 'Avis',
                'kicker' => 'Table Noter',
                'entity' => Noter::class,
                'form' => NoterType::class,
                'order' => ['dateMessage' => 'DESC'],
                'columns' => ['ID', 'Produit', 'Client', 'Message', 'Date'],
                'empty' => 'Aucun avis enregistré.',
                'new_title' => 'Nouvel_Avis',
                'edit_title' => 'Modifier_Avis',
            ],
            'sav' => [
                'active' => 'support',
                'title' => 'SAV',
                'kicker' => 'Table Sav',
                'entity' => Sav::class,
                'form' => SavType::class,
                'order' => ['dateMessage' => 'DESC'],
                'columns' => ['ID', 'Commande', 'Client', 'Message', 'Traitement', 'Date'],
                'empty' => 'Aucune demande SAV enregistrée.',
                'new_title' => 'Nouveau_SAV',
                'edit_title' => 'Modifier_SAV',
            ],
            'contacts' => [
                'active' => 'contacts',
                'title' => 'Contacts',
                'kicker' => 'Table Contact // Messages publics',
                'entity' => Contact::class,
                'form' => ContactType::class,
                'order' => ['dateEnvoi' => 'DESC'],
                'columns' => ['ID', 'Nom', 'Prénom', 'Sujet', 'Message', 'Date'],
                'empty' => 'Aucun message de contact enregistré.',
                'new_title' => 'Nouveau_Contact',
                'edit_title' => 'Modifier_Contact',
            ],
            'admins' => [
                'active' => 'permissions',
                'title' => 'Administrateurs',
                'kicker' => 'Table Admin // rôles User',
                'entity' => Admin::class,
                'form' => AdminType::class,
                'order' => ['id' => 'DESC'],
                'columns' => ['ID', 'Email admin', 'Utilisateur lié'],
                'empty' => 'Aucun profil admin enregistré.',
                'new_title' => 'Nouveau_Admin',
                'edit_title' => 'Modifier_Admin',
            ],
        ];

        if (!isset($configs[$resource])) {
            throw new NotFoundHttpException('Ressource admin inconnue.');
        }

        return $configs[$resource];
    }

    private function newEntity(string $class): object
    {
        return match ($class) {
            User::class => (new User())->setRoles(['ROLE_USER'])->setIsVerified(true),
            Panier::class => (new Panier())->setTotalHtPa('0.00'),
            Ajouter::class => (new Ajouter())->setQuantite(1)->setPrixHt('0.00'),
            Commande::class => (new Commande())->setDateCommande(new \DateTime())->setTotalHtCo('0.00')->setTotalTaxe('0.00')->setTotal('0.00'),
            Parvenir::class => (new Parvenir())->setQuantite(1)->setPrixHt('0.00'),
            Noter::class => (new Noter())->setDateMessage(new \DateTime()),
            Sav::class => (new Sav())->setDateMessage(new \DateTime())->setTraitement('Nouveau'),
            Contact::class => (new Contact())->setDateEnvoi(new \DateTime()),
            default => new $class(),
        };
    }

    /**
     * @param object[] $items
     * @return array<int, array{id: int|null, cells: string[]}>
     */
    private function rows(string $resource, array $items): array
    {
        return array_map(fn (object $item): array => [
            'id' => $this->entityId($item),
            'cells' => $this->rowCells($resource, $item),
        ], $items);
    }

    /**
     * @return string[]
     */
    private function rowCells(string $resource, object $item): array
    {
        return match ($resource) {
            'produits' => [
                (string) $item->getId(),
                $item->getDesignation() ?? '',
                $item->getCategorie()?->getNomCategorie() ?? 'Sans catégorie',
                $this->formatMoney($item->getPrixUnitHT()),
                (string) $item->getStock(),
            ],
            'categories' => [
                (string) $item->getId(),
                $item->getNomCategorie() ?? '',
                (string) $item->getProduits()->count(),
            ],
            'utilisateurs' => [
                (string) $item->getId(),
                trim(($item->getPrenom() ?? '') . ' ' . ($item->getNom() ?? '')) ?: 'Profil incomplet',
                $item->getEmail() ?? '',
                implode(', ', $item->getRoles()),
                (string) $item->getAdresses()->count(),
                (string) $item->getCommandes()->count(),
            ],
            'adresses' => [
                (string) $item->getId(),
                $item->getUser()?->getEmail() ?? 'Client supprimé',
                $item->getRue() ?? '',
                trim(($item->getCp() ?? '') . ' ' . ($item->getVille() ?? '')),
                $item->getPays() ?? '',
            ],
            'paniers' => [
                (string) $item->getId(),
                $item->getUser()?->getEmail() ?? 'Client supprimé',
                $this->formatMoney($item->getTotalHtPa()),
                (string) $item->getAjouters()->count(),
            ],
            'ajouter' => [
                (string) $item->getId(),
                '#' . ($item->getPanier()?->getId() ?? '-'),
                $item->getProduit()?->getDesignation() ?? 'Produit supprimé',
                (string) $item->getQuantite(),
                $this->formatMoney($item->getPrixHt()),
            ],
            'commandes' => [
                (string) $item->getId(),
                $this->formatDate($item->getDateCommande()),
                $item->getUser()?->getEmail() ?? 'Client supprimé',
                $item->getAdresse()?->getVille() ?? 'Adresse supprimée',
                $this->formatMoney($item->getTotalHtCo()),
                $this->formatMoney($item->getTotalTaxe()),
                $this->formatMoney($item->getTotal()),
            ],
            'parvenir' => [
                (string) $item->getId(),
                '#SC-' . ($item->getCommande()?->getId() ?? '-'),
                $item->getProduit()?->getDesignation() ?? 'Produit supprimé',
                (string) $item->getQuantite(),
                $this->formatMoney($item->getPrixHt()),
            ],
            'avis' => [
                (string) $item->getId(),
                $item->getProduit()?->getDesignation() ?? 'Produit supprimé',
                $item->getUser()?->getEmail() ?? 'Client supprimé',
                $this->shorten($item->getMessage()),
                $this->formatDate($item->getDateMessage()),
            ],
            'sav' => [
                (string) $item->getId(),
                '#SC-' . ($item->getCommande()?->getId() ?? '-'),
                $item->getCommande()?->getUser()?->getEmail() ?? 'Client supprimé',
                $this->shorten($item->getMessage()),
                $item->getTraitement() ?? 'Nouveau',
                $this->formatDate($item->getDateMessage()),
            ],
            'contacts' => [
                (string) $item->getId(),
                $item->getNom() ?? '',
                $item->getPrenom() ?? '',
                $item->getSujet() ?? '',
                $this->shorten($item->getMessage()),
                $this->formatDate($item->getDateEnvoi()),
            ],
            'admins' => [
                (string) $item->getId(),
                $item->getEmailA() ?? '',
                $item->getUser()?->getEmail() ?? 'Utilisateur supprimé',
            ],
            default => [],
        };
    }

    private function entityId(object $entity): ?int
    {
        return method_exists($entity, 'getId') ? $entity->getId() : null;
    }

    private function formatDate(?\DateTimeInterface $date): string
    {
        return $date ? $date->format('d/m/Y H:i') : 'Date non renseignée';
    }

    private function formatMoney(?string $amount): string
    {
        return $amount !== null && $amount !== '' ? number_format((float) $amount, 2, ',', ' ') . ' €' : '0,00 €';
    }

    private function shorten(?string $value, int $limit = 90): string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return 'Message vide';
        }

        if (strlen($text) <= $limit) {
            return $text;
        }

        return substr($text, 0, $limit - 3) . '...';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function adminSections(EntityManagerInterface $entityManager): array
    {
        $resources = ['produits', 'categories', 'commandes', 'utilisateurs', 'adresses', 'paniers', 'ajouter', 'parvenir', 'avis', 'sav', 'contacts', 'admins'];

        return array_map(function (string $resource) use ($entityManager): array {
            $config = $this->resourceConfig($resource);

            return [
                'label' => $config['title'],
                'description' => $config['kicker'],
                'icon' => match ($resource) {
                    'produits' => 'inventory_2',
                    'categories' => 'category',
                    'commandes' => 'receipt_long',
                    'utilisateurs' => 'group',
                    'adresses' => 'location_on',
                    'paniers' => 'shopping_cart',
                    'ajouter', 'parvenir' => 'format_list_bulleted',
                    'avis' => 'rate_review',
                    'sav' => 'support_agent',
                    'contacts' => 'contact_mail',
                    'admins' => 'admin_panel_settings',
                    default => 'table',
                },
                'resource' => $resource,
                'count' => $entityManager->getRepository($config['entity'])->count([]),
                'active' => true,
            ];
        }, $resources);
    }
}
