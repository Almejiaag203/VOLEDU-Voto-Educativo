<!-- Core scripts -->
<script src="assets/js/pace.js"></script>
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/libs/popper/popper.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/sidenav.js"></script>
<script src="assets/js/layout-helpers.js"></script>
<script src="assets/js/material-ripple.js"></script>

<!-- Libs -->
<script src="assets/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/libs/eve/eve.js"></script>
<script src="assets/libs/flot/flot.js"></script>
<script src="assets/libs/flot/curvedLines.js"></script>
<script src="assets/libs/chart-am4/core.js"></script>
<script src="assets/libs/chart-am4/charts.js"></script>
<script src="assets/libs/chart-am4/animated.js"></script>

<!-- Demo -->
<script src="assets/js/demo.js"></script>
<script src="assets/js/analytics.js"></script>
<script src="assets/js/pages/dashboards_index.js"></script>

<!-- Footer -->
<nav class="layout-footer footer bg-white">
    <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
        <div class="pt-3">
            <span class="footer-text font-weight-semibold">&copy; <a href="https://www.facebook.com/TechFusionData" class="footer-link" target="_blank">TehFusion Data</a></span>
        </div>
        <div>
            <a href="javascript:void(0);" class="footer-link pt-3" data-toggle="modal" data-target="#aboutUsModal">About Us</a>
            <a href="javascript:void(0);" class="footer-link pt-3 ml-4" data-toggle="modal" data-target="#helpModal">Help</a>
            <a href="javascript:void(0);" class="footer-link pt-3 ml-4" data-toggle="modal" data-target="#contactModal">Contact</a>
            <a href="javascript:void(0);" class="footer-link pt-3 ml-4" data-toggle="modal" data-target="#termsModal">Terms &amp; Conditions</a>
        </div>
    </div>
</nav>

<!-- Modals -->
<!-- About Us Modal -->
<div class="modal fade" id="aboutUsModal" tabindex="-1" role="dialog" aria-labelledby="aboutUsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="modal-header bg-light" style="border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="aboutUsModalLabel">Acerca de Nosotros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; line-height: 1.6;">
                <h6 class="font-weight-semibold mb-3">Nuestra Misión | Sobre VOLEDU</h6>
                <p>VOLEDU (Votación Educativa) es un proyecto tecnológico creado para impulsar la transparencia y la eficiencia en los procesos democráticos de nuestro colegio. Nuestro objetivo es modernizar la elección de representantes, garantizando resultados instantáneos, seguros y accesibles para toda la comunidad estudiantil.</p>
                <h6 class="font-weight-semibold mb-3">Valores Fundamentales:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><span class="font-weight-bold">Transparencia:</span> Conteo de votos en tiempo real.</li>
                    <li class="mb-2"><span class="font-weight-bold">Seguridad:</span> Uso de encriptación avanzada para proteger el anonimato del votante.</li>
                    <li class="mb-2"><span class="font-weight-bold">Inclusión:</span> Acceso fácil desde cualquier dispositivo.</li>
                    <li><span class="font-weight-bold">Educación:</span> Fomentar una cultura de participación cívica y responsabilidad.</li>
                </ul>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" style="border-radius: 5px;" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="modal-header bg-light" style="border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="helpModalLabel">Ayuda y Preguntas Frecuentes - FAQ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; line-height: 1.6;">
                <div class="accordion" id="faqAccordion">
                    <div class="card mb-2" style="border: none;">
                        <div class="card-header bg-white" id="faq1">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-dark font-weight-semibold" style="text-decoration: none; width: 100%; text-align: left;" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    ¿Cómo puedo ingresar al sistema?
                                </button>
                            </h6>
                        </div>
                        <div id="collapse1" class="collapse show" aria-labelledby="faq1" data-parent="#faqAccordion">
                            <div class="card-body">
                                Utiliza tu código de estudiante (o DNI) y la contraseña proporcionada por la Administración.
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2" style="border: none;">
                        <div class="card-header bg-white" id="faq2">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-dark font-weight-semibold" style="text-decoration: none; width: 100%; text-align: left;" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    ¿Mi voto es anónimo? ¿Se rastreará mi elección?
                                </button>
                            </h6>
                        </div>
                        <div id="collapse2" class="collapse" aria-labelledby="faq2" data-parent="#faqAccordion">
                            <div class="card-body">
                                Absolutamente no. El sistema utiliza encriptación avanzada para garantizar el secreto total del voto. Su elección es anónima e imposible de vincular a su identidad una vez confirmada.
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2" style="border: none;">
                        <div class="card-header bg-white" id="faq3">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-dark font-weight-semibold" style="text-decoration: none; width: 100%; text-align: left;" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    ¿Puedo votar más de una vez?
                                </button>
                            </h6>
                        </div>
                        <div id="collapse3" class="collapse" aria-labelledby="faq3" data-parent="#faqAccordion">
                            <div class="card-body">
                                No. Una vez que confirmas tu voto, el sistema te marca inmediatamente en el padrón como "votó", bloqueando cualquier intento de reingreso.
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2" style="border: none;">
                        <div class="card-header bg-white" id="faq4">
                            <h6 class="mb-0">
                                <button class="btn btn-link text-dark font-weight-semibold" style="text-decoration: none; width: 100%; text-align: left;" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    Cerré el navegador sin terminar, ¿qué hago?
                                </button>
                            </h6>
                        </div>
                        <div id="collapse4" class="collapse" aria-labelledby="faq4" data-parent="#faqAccordion">
                            <div class="card-body">
                                Si aún no has llegado a la pantalla de confirmación final, puedes volver a ingresar y continuar el proceso. Una vez confirmado, no puedes modificar tu voto.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" style="border-radius: 5px;" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="modal-header bg-light" style="border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="contactModalLabel">Contacto y Soporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; line-height: 1.6;">
                <h6 class="font-weight-semibold mb-3">Canales de Soporte Técnico</h6>
                <p>Si tienes problemas técnicos para acceder o inconvenientes con el funcionamiento del sistema VOLEDU, contacta directamente al equipo desarrollador:</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><span class="font-weight-bold">Empresa de Soporte:</span> TechFusion Data</li>
                    <li class="mb-2"><span class="font-weight-bold">Teléfono de Soporte:</span> 919525157</li>
                    <li class="mb-2"><span class="font-weight-bold">Correo Electrónico:</span> techfusiondata.technology@gmail.com</li>
                    <li><span class="font-weight-bold">Horario de Atención:</span> De lunes a viernes, de 8:00 a.m. a 5:00 p.m.</li>
                </ul>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" style="border-radius: 5px;" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="modal-header bg-light" style="border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="termsModalLabel">Términos y Condiciones de Uso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; line-height: 1.6;">
                <h6 class="font-weight-semibold mb-3">Términos de Uso del Sistema de Votación Escolar VOLEDU</h6>
                <h6 class="font-weight-semibold mb-2">1. Aceptación de los Términos</h6>
                <p>Al acceder y utilizar el sistema VOLEDU, usted acepta cumplir con los presentes Términos y Condiciones, así como con el Reglamento de Elecciones del Colegio.</p>
                <h6 class="font-weight-semibold mb-2">2. Principios de Seguridad y Privacidad</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><span class="font-weight-bold">Anonimato del Voto:</span> El sistema garantiza que la elección se maneja de forma anónima. La tecnología de encriptación asegura que es imposible vincular un voto específico a un usuario.</li>
                    <li class="mb-2"><span class="font-weight-bold">Uso de Datos:</span> El sistema solo utiliza los datos de identificación proporcionados por el Padrón Electoral con el único fin de validar el derecho a voto y prevenir la doble votación.</li>
                    <li><span class="font-weight-bold">Integridad del Proceso:</span> El sistema está diseñado para ser a prueba de manipulaciones. Cualquier intento de fraude o alteración será reportado.</li>
                </ul>
                <h6 class="font-weight-semibold mb-2">3. Responsabilidades del Usuario</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><span class="font-weight-bold">Uso de Credenciales:</span> El estudiante/votante es responsable de la confidencialidad de su código de acceso y contraseña.</li>
                    <li class="mb-2"><span class="font-weight-bold">Uso Fraudulento:</span> Compartir credenciales o intentar votar con credenciales ajenas se considera una falta grave contra el reglamento escolar y la ética democrática.</li>
                    <li><span class="font-weight-bold">Voto Único:</span> Al confirmar su voto, el votante acepta que su decisión es final y no puede ser modificada.</li>
                </ul>
                <h6 class="font-weight-semibold mb-2">4. Jurisdicción</h6>
                <p>Cualquier controversia o duda sobre la aplicación de los resultados o del reglamento electoral se resolverá conforme a las directrices y el Reglamento Interno del Colegio.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" style="border-radius: 5px;" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>