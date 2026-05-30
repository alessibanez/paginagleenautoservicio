# Automotriz Gleen — Sitio Web

Sitio web estático para **Automotriz Gleen**, taller de servicio automotriz en Toluca. Basado en el template CarServ de HTML Codex, adaptado al diseño de la marca.

---

## Changelog

### v0.2 — Datos reales del taller, nueva paleta y rediseño de secciones

**Todos los HTML (8 archivos)**
- Datos de contacto actualizados en topbar y footer de todas las páginas:
  - Dirección: Av. Independencia 1313-A, Col. Reforma y Ferrocarriles Nacionales, C.P. 50070, Toluca, Edo. Méx.
  - Teléfono: 722 534-3858
  - Correo: contacto@gleenautomotriz.com
  - Horario: Lunes–Viernes 09:00–18:00 / Sábados 09:00–14:00 hrs.
- Etiquetas de horario y dirección traducidas al español en footer

**css/style.css + css/bootstrap.min.css**
- Paleta de colores completamente reemplazada (rojo/navy → azul/amarillo):
  - `--primary`: `#D81324` → `#1985C0` (azul medio)
  - `--secondary`: `#0B2154` → `#F5C414` (amarillo/dorado)
  - `--dark`: `#111111` → `#1A3F70` (azul marino)
- Texto de `.btn-secondary` cambiado a color oscuro para contraste correcto sobre fondo amarillo
- 52 ocurrencias de colores hardcodeados actualizadas en `bootstrap.min.css`

**index.html**
- Sección Fact (contadores 1234) eliminada
- Sección About rediseñada:
  - Layout: imagen a la izquierda, texto a la derecha
  - Título real: "Gleen, su mejor opción en servicios automotrices"
  - Descripción real del taller
  - Eliminados: lista numerada (01/02/03), badge "15 Años", botón "Leer Más"
  - Imagen placeholder: `about.jpg` (pendiente asset `about-cars.jpg`)
- Tarjetas de contacto actualizadas: Dirección / Teléfono / Correo
- Mapa actualizado a dirección real de Toluca

**contact.html**
- Tarjetas de info rediseñadas: Dirección / Teléfono / Correo
- Tarjeta Correo incluye texto: "Realiza una cotización, envía un correo a nuestra dirección."
- Mapa actualizado a dirección real de Toluca
- Formulario traducido al español (labels, placeholders, botón)

---

### v0.1 — Primera sincronización con el diseño de Canva

**index.html**
- Idioma cambiado a `es`
- Actualización de Font Awesome 5.10 → 6.4
- **Hero reemplazado**: se eliminó el carrusel de dos slides y se sustituyó por una sección estática `.hero-section` con imagen de fondo, overlay oscuro, titular "SERVICIO AUTOMOTRIZ", botón WhatsApp y botón "CONTÁCTANOS"
- Logo de la marca (`img/logo-gleen.png`) como imagen en la columna derecha del hero (pendiente de asset)
- Mini-servicios actualizados: íconos y textos reemplazados por Mantenimiento, Reparación y Refacciones/Accesorios

**css/style.css**
- Navbar siempre visible desde el inicio (`top: -100px` → `top: 0`)
- Nuevos estilos para `.hero-section` y `.hero-logo`
- Botón `.btn-whatsapp` (verde #25D366)
- Botones flotantes fijos: `.btn-float-whatsapp` y `.btn-float-appointment`
- Stack de imágenes para sección Testimonial (`.testimonial-img-stack`, `.stack-img--back/mid/front`)
- Íconos de redes sociales estáticos (`.social-static`) para TikTok, Instagram y Facebook

**js/main.js**
- Eliminado el scroll listener que mostraba/ocultaba la navbar: ahora siempre está visible vía CSS

**about.html**
- Eliminada la sección de mini-servicios redundante (3 tarjetas de características)

---

## Paginas

| Archivo | Descripcion |
|---|---|
| `index.html` | Pagina principal: hero carousel, servicios destacados, about, booking, equipo y testimonios |
| `about.html` | Pagina acerca del taller |
| `service.html` | Listado completo de servicios |
| `booking.html` | Formulario de reserva de turno |
| `team.html` | Equipo de tecnicos |
| `testimonial.html` | Testimonios de clientes |
| `contact.html` | Informacion de contacto con mapa embebido |
| `404.html` | Pagina de error 404 |

## Estructura de archivos

```
carserv-1.0.0/
├── index.html
├── about.html
├── service.html
├── booking.html
├── team.html
├── testimonial.html
├── contact.html
├── 404.html
├── css/
│   ├── bootstrap.min.css   # Bootstrap 5 customizado
│   └── style.css           # Estilos propios del template
├── js/
│   └── main.js             # Inicializacion de librerias (WOW, Owl Carousel, etc.)
├── img/                    # Imagenes del sitio
└── lib/                    # Librerias JS/CSS de terceros
    ├── animate/
    ├── counterup/
    ├── easing/
    ├── owlcarousel/
    ├── tempusdominus/      # Date picker para formulario de booking
    ├── waypoints/
    └── wow/
```

## Dependencias usadas (CDN)

- [Bootstrap 5.0](https://getbootstrap.com/)
- [Font Awesome 5.10](https://fontawesome.com/)
- [Bootstrap Icons 1.4](https://icons.getbootstrap.com/)
- [jQuery 3.4.1](https://jquery.com/)
- [WOW.js](https://wowjs.uk/) — animaciones al hacer scroll
- [Owl Carousel 2](https://owlcarousel2.github.io/OwlCarousel2/) — carrusel de testimonios
- [Tempus Dominus](https://getdatepicker.com/) — date picker en el formulario de booking
- Google Fonts: Barlow (600, 700) + Ubuntu (400, 500)

