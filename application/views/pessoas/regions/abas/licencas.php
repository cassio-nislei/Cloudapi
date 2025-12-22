<div class="row">
    <div class="col-md-12">
        <table v-if="registro.LISTA_LICENCAS && (registro.LISTA_LICENCAS.length ?? 0) > 0" class="table table-condensed table-hover"
               style="font-size: 12px;"
               >
            <thead>
                <tr>
                    <th style="border-top: none;">Hostname</th>
                    <th style="border-top: none;">Chave</th>
                    <th style="border-top: none;">Último Login</th>
                    <!--
                    <th style="border-top: none;">Status</th>                    
                    -->
                    <th style="border-top: none;"></th> 
                </tr>
            </thead>
            <tbody>
                <tr v-for="licenca in registro.LISTA_LICENCAS">
                    <td>{{licenca.HOSTNAME}}</td>
                    <td>{{licenca.GUID}}</td>
                    <td>{{licenca.LAST_LOGIN}}</td>
                    <!--
                    <td v-on:click="changeStatus(licenca.ID)" title="Alterar Status" style="cursor: pointer;">
                        <span v-if="licenca.STATUS === 'A'" class="badge badge-success" style="width: 80px;">
                            Ativo
                        </span>
                        <span v-else class="badge badge-danger" style="width: 80px;">
                            Desativado
                        </span>
                    </td>
                    -->
                    <td>
                        <span class="fa fa-trash" v-on:click="excluirLicenca(licenca.ID)"
                              title="Excluir Licença"
                              style="cursor: pointer;"
                              ></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else style="text-align: center;">
            <br>
            Nenhuma Licença encontrada.
        </div>
    </div>
</div>