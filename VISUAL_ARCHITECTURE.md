# Import Data Module - Visual Architecture

## Module Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     IMPORT DATA MODULE                          │
│                    (OpenCart 2.x Module)                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                           │
│                      (4-Step Wizard)                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐ │
│  │  STEP 1  │ -> │  STEP 2  │ -> │  STEP 3  │ -> │  STEP 4  │ │
│  │  Upload  │    │  Mapping │    │ Settings │    │  Import  │ │
│  └──────────┘    └──────────┘    └──────────┘    └──────────┘ │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              |
                              v
┌─────────────────────────────────────────────────────────────────┐
│                         CONTROLLER                              │
│           (admin/controller/extension/module/)                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  • index()        - Step 1: File Upload Interface              │
│  • upload()       - Handle File Upload & Validation            │
│  • mapping()      - Step 2: Column Mapping Interface           │
│  • settings()     - Step 3: Import Settings Interface          │
│  • process()      - Step 4: Execute Import Process             │
│  • install()      - Module Installation                        │
│  • uninstall()    - Module Uninstallation                      │
│  • history()      - View Import History                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              |
                              v
┌─────────────────────────────────────────────────────────────────┐
│                           MODEL                                 │
│             (admin/model/extension/module/)                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  File Processing:                                               │
│  • parseFileHeaders()    - Extract column headers              │
│  • getSampleData()       - Get preview data                    │
│  • readFileData()        - Read entire file                    │
│                                                                 │
│  Import Logic:                                                  │
│  • processImport()       - Main import orchestrator            │
│  • importRow()           - Import single row                   │
│  • importProduct()       - Import product entity               │
│  • importCategory()      - Import category entity              │
│  • importCustomer()      - Import customer entity              │
│                                                                 │
│  Data Management:                                               │
│  • getEntityFields()     - Get DB fields for entity            │
│  • mapRowData()          - Map file data to DB fields          │
│  • insertProduct()       - Create new product                  │
│  • updateProduct()       - Update existing product             │
│                                                                 │
│  History & Logging:                                             │
│  • createImportHistory() - Log import operation                │
│  • updateImportHistory() - Update import status                │
│  • logImportError()      - Log row-level errors                │
│  • getImportHistory()    - Retrieve import logs                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              |
                              v
┌─────────────────────────────────────────────────────────────────┐
│                         DATABASE                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Import Tracking:                                               │
│  ┌────────────────────────┐                                    │
│  │  oc_import_history     │                                    │
│  ├────────────────────────┤                                    │
│  │ - import_id            │                                    │
│  │ - entity_type          │                                    │
│  │ - filename             │                                    │
│  │ - records_inserted     │                                    │
│  │ - records_updated      │                                    │
│  │ - records_errors       │                                    │
│  │ - status               │                                    │
│  │ - date_started         │                                    │
│  └────────────────────────┘                                    │
│                                                                 │
│  Error Logging:                                                 │
│  ┌────────────────────────┐                                    │
│  │  oc_import_errors      │                                    │
│  ├────────────────────────┤                                    │
│  │ - error_id             │                                    │
│  │ - import_id            │                                    │
│  │ - row_number           │                                    │
│  │ - error_message        │                                    │
│  │ - row_data             │                                    │
│  └────────────────────────┘                                    │
│                                                                 │
│  Entity Tables (Target):                                        │
│  • oc_product                                                   │
│  • oc_product_description                                       │
│  • oc_product_to_category                                       │
│  • oc_category                                                  │
│  • oc_customer                                                  │
│  • etc...                                                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

## Data Flow Diagram

```
┌──────────────┐
│  User Uploads│
│  CSV/Excel   │
└──────┬───────┘
       │
       v
┌──────────────────────┐
│  File Validation     │
│  • Type check        │
│  • Size check        │
└──────┬───────────────┘
       │
       v
┌──────────────────────┐
│  Parse Headers       │
│  • Read first row    │
│  • Detect columns    │
└──────┬───────────────┘
       │
       v
┌──────────────────────┐
│  Column Mapping      │
│  • Auto-detect       │
│  • Manual adjust     │
└──────┬───────────────┘
       │
       v
┌──────────────────────┐
│  Configure Settings  │
│  • Import mode       │
│  • Options           │
└──────┬───────────────┘
       │
       v
┌──────────────────────┐
│  Read File Data      │
│  • Stream reading    │
│  • Batch processing  │
└──────┬───────────────┘
       │
       v
┌──────────────────────┐
│  Process Each Row    │
│  • Map data          │
│  • Validate          │
│  • Insert/Update     │
└──────┬───────────────┘
       │
       ├─> Success -> Log to history
       │
       └─> Error -> Log to errors
       │
       v
┌──────────────────────┐
│  Display Results     │
│  • Statistics        │
│  • Error summary     │
└──────────────────────┘
```

## Wizard Flow Diagram

```
START
  │
  v
┌─────────────────────┐
│   STEP 1: UPLOAD    │
├─────────────────────┤
│ • Select Entity     │
│ • Choose File       │
│ • View History      │
└──────┬──────────────┘
       │
       │ [File Uploaded]
       │
       v
┌─────────────────────┐
│  STEP 2: MAPPING    │
├─────────────────────┤
│ • Auto-map Columns  │
│ • Manual Adjust     │
│ • Preview Data      │
└──────┬──────────────┘
       │
       │ [Mapping Confirmed]
       │
       v
┌─────────────────────┐
│  STEP 3: SETTINGS   │
├─────────────────────┤
│ • Import Mode       │
│   - Insert Only     │
│   - Update Only     │
│   - Upsert          │
│ • Advanced Options  │
│   - Skip Header     │
│   - Delete Existing │
│   - Continue Errors │
│   - Batch Size      │
└──────┬──────────────┘
       │
       │ [Start Import]
       │
       v
┌─────────────────────┐
│   STEP 4: IMPORT    │
├─────────────────────┤
│ • Process Records   │
│ • Show Progress     │
│ • Log Errors        │
│ • Display Results   │
└──────┬──────────────┘
       │
       v
     END
```

## Import Mode Flow

```
For Each Row in File:
  │
  ├─> [INSERT ONLY MODE]
  │   │
  │   └─> Record Exists? ──Yes──> Skip
  │       │
  │       No
  │       │
  │       └──> Insert Record
  │
  ├─> [UPDATE ONLY MODE]
  │   │
  │   └─> Record Exists? ──No──> Skip
  │       │
  │       Yes
  │       │
  │       └──> Update Record
  │
  └─> [UPSERT MODE]
      │
      └─> Record Exists? 
          │
          ├──Yes──> Update Record
          │
          └──No───> Insert Record
```

## File Processing Flow

```
┌────────────────┐
│  Upload File   │
└────────┬───────┘
         │
         v
┌────────────────────┐      ┌──────────────┐
│  Detect Format     │      │   CSV/TXT    │
└────────┬───────────┘      └──────────────┘
         │                          │
         │                          v
         │                  ┌──────────────┐
         │                  │ fopen()      │
         │                  │ fgetcsv()    │
         │                  └──────────────┘
         │
         ├─────────────────────────────────┐
         │                                 │
         v                                 v
┌────────────────┐              ┌──────────────────┐
│   XLSX/XLS     │              │   Read Headers   │
└────────┬───────┘              └────────┬─────────┘
         │                               │
         v                               v
┌────────────────┐              ┌──────────────────┐
│  PHPExcel      │              │   Read Rows      │
│  Load File     │              │   (Batched)      │
└────────┬───────┘              └────────┬─────────┘
         │                               │
         v                               v
┌────────────────┐              ┌──────────────────┐
│  Read Worksheet│              │  Process Each    │
└────────┬───────┘              │  Row             │
         │                      └────────┬─────────┘
         └──────────────────────────────┘
                                │
                                v
                        ┌──────────────┐
                        │  Map Data    │
                        └──────┬───────┘
                               │
                               v
                        ┌──────────────┐
                        │  Insert/     │
                        │  Update DB   │
                        └──────────────┘
```

## Entity Processing Flow

```
Product Import:
  │
  ├─> Validate Required Fields
  │   │
  │   └─> Missing? -> Log Error
  │
  ├─> Check Product Exists
  │   │
  │   ├─> By product_id
  │   └─> By model
  │
  ├─> Apply Import Mode
  │   │
  │   ├─> Insert: Create new product
  │   │   │
  │   │   ├─> Insert into oc_product
  │   │   ├─> Insert into oc_product_description
  │   │   ├─> Insert into oc_product_to_store
  │   │   └─> Insert into oc_product_to_category
  │   │
  │   └─> Update: Modify existing
  │       │
  │       ├─> Update oc_product
  │       └─> Update oc_product_description
  │
  └─> Log Result
      │
      ├─> Success: Increment counter
      └─> Error: Log to import_errors
```

## Error Handling Flow

```
During Import:
  │
  Try:
  │
  ├─> Process Row
  │   │
  │   └─> Database Operation
  │
  Catch Error:
  │
  ├─> Log Error
  │   │
  │   ├─> Save to oc_import_errors
  │   │   • import_id
  │   │   • row_number
  │   │   • error_message
  │   │   • row_data
  │   │
  │   └─> Increment error counter
  │
  ├─> Check Setting
  │   │
  │   ├─> Continue on Errors = YES
  │   │   │
  │   │   └─> Continue to next row
  │   │
  │   └─> Continue on Errors = NO
  │       │
  │       └─> Stop import, rollback
  │
  Finally:
  │
  └─> Update import_history
      • records_errors
      • status
      • date_completed
```

## Directory Structure

```
opencart/
│
├── admin/
│   ├── controller/
│   │   └── extension/
│   │       └── module/
│   │           └── import_data.php ············ Controller
│   │
│   ├── model/
│   │   └── extension/
│   │       └── module/
│   │           └── import_data.php ············ Model
│   │
│   ├── view/
│   │   └── template/
│   │       └── extension/
│   │           └── module/
│   │               ├── import_data.tpl ········ Step 1 View
│   │               ├── import_data_mapping.tpl  Step 2 View
│   │               └── import_data_settings.tpl Step 3 View
│   │
│   └── language/
│       └── en-gb/
│           └── extension/
│               └── module/
│                   └── import_data.php ········ Language File
│
├── system/
│   ├── storage/
│   │   └── upload/
│   │       └── import/ ··················· Upload Directory
│   │
│   └── modification_import_data.xml ······ System Modification
│
├── extension/
│   └── opencart/
│       └── install_import_data.xml ······· Install Config
│
└── Documentation/
    ├── OPENCART_MODULE_DOCUMENTATION.md
    ├── IMPORT_DATA_MODULE_README.md
    ├── INSTALLATION_GUIDE.md
    ├── PROJECT_SUMMARY.md
    ├── VISUAL_ARCHITECTURE.md (this file)
    ├── sample_products_import.csv
    └── sample_categories_import.csv
```

## Component Interaction

```
┌─────────┐     HTTP Request      ┌────────────┐
│ Browser │ ─────────────────────> │ Controller │
└─────────┘                        └──────┬─────┘
     ^                                    │
     │                                    │ Load Model
     │                                    v
     │                             ┌────────────┐
     │                             │   Model    │
     │                             └──────┬─────┘
     │                                    │
     │                                    │ Database Query
     │                                    v
     │                             ┌────────────┐
     │                             │  Database  │
     │                             └──────┬─────┘
     │                                    │
     │                                    │ Return Data
     │                                    v
     │                             ┌────────────┐
     │      Render HTML            │    View    │
     └─────────────────────────────┤  Template  │
                                   └────────────┘
```

## Session Data Flow

```
Step 1 (Upload):
  │
  └─> Store in Session:
      • filename
      • filepath
      • entity_type
      • file_extension
      • headers

Step 2 (Mapping):
  │
  └─> Add to Session:
      • mapping (array)

Step 3 (Settings):
  │
  └─> Add to Session:
      • settings (array)
        - import_mode
        - skip_first_row
        - batch_size
        - delete_existing
        - update_existing
        - ignore_errors

Step 4 (Process):
  │
  ├─> Read from Session
  ├─> Execute Import
  └─> Clear Session
```

## Key Design Patterns

### 1. MVC Pattern
```
Model      : Data & Business Logic
View       : Presentation Layer
Controller : Request Handler
```

### 2. Wizard Pattern
```
Multi-step process with:
• State preservation (session)
• Sequential navigation
• Progress indication
• Data validation per step
```

### 3. Batch Processing
```
Large Dataset → Split into Batches → Process → Repeat
```

### 4. Strategy Pattern (Import Modes)
```
Interface: ImportStrategy
├─> InsertOnlyStrategy
├─> UpdateOnlyStrategy
└─> UpsertStrategy
```

---

**This visual documentation provides a comprehensive overview of the module's architecture, data flows, and component interactions.**
